<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * This file contains tests for the 'missing' behaviour.
 *
 * @package qbehaviour_missing
 * @copyright 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../../../engine/lib.php');
require_once(dirname(__FILE__) . '/../../../engine/simpletest/helpers.php');
require_once(dirname(__FILE__) . '/../behaviour.php');

class qbehaviour_missing_test extends UnitTestCase {
    public function test_missing_cannot_start() {
        $qa = new question_attempt(test_question_maker::make_a_truefalse_question(), 0);
        $behaviour = new qbehaviour_missing($qa, 'deferredfeedback');
        $this->expectException();
        $behaviour->init_first_step(new question_attempt_step(array()));
    }

    public function test_missing_cannot_process() {
        $qa = new question_attempt(test_question_maker::make_a_truefalse_question(), 0);
        $behaviour = new qbehaviour_missing($qa, 'deferredfeedback');
        $this->expectException();
        $behaviour->process_action(new question_attempt_pending_step(array()));
    }

    public function test_missing_cannot_get_min_grade() {
        $qa = new question_attempt(test_question_maker::make_a_truefalse_question(), 0);
        $behaviour = new qbehaviour_missing($qa, 'deferredfeedback');
        $this->expectException();
        $behaviour->get_min_fraction();
    }

    public function test_render_missing() {
        $records = testing_db_record_builder::build_db_records(array(
            array('id', 'questionattemptid', 'questionusageid', 'slot',
                              'behaviour', 'questionid', 'maxmark', 'minfraction', 'flagged',
                                                                            'questionsummary', 'rightanswer', 'responsesummary', 'timemodified',
                                                                                                   'attemptstepid', 'sequencenumber', 'state', 'fraction',
                                                                                                                          'timecreated', 'userid', 'name', 'value'),
            array(1, 1, 1, 1, 'strangeunknown', -1, 2.0000000, 0.0000000, 0, '', '', '', 1256233790, 1, 0, 'todo',     null, 1256233700, 1,   '_order', '1,2,3'),
            array(2, 1, 1, 1, 'strangeunknown', -1, 2.0000000, 0.0000000, 0, '', '', '', 1256233790, 2, 1, 'complete', 0.50, 1256233705, 1,  '-submit',  '1'),
            array(3, 1, 1, 1, 'strangeunknown', -1, 2.0000000, 0.0000000, 0, '', '', '', 1256233790, 2, 1, 'complete', 0.50, 1256233705, 1,  'choice0',  '1'),
        ));

        $question = test_question_maker::make_a_truefalse_question();
        $question->id = -1;

        question_bank::start_unit_test();
        question_bank::load_test_question_data($question);
        $qa = question_attempt::load_from_records($records, 1,
                new question_usage_null_observer(), 'deferredfeedback');
        question_bank::end_unit_test();

        $this->assertEqual(2, $qa->get_num_steps());

        $step = $qa->get_step(0);
        $this->assertEqual(question_state::$todo, $step->get_state());
        $this->assertNull($step->get_fraction());
        $this->assertEqual(1256233700, $step->get_timecreated());
        $this->assertEqual(1, $step->get_user_id());
        $this->assertEqual(array('_order' => '1,2,3'), $step->get_all_data());

        $step = $qa->get_step(1);
        $this->assertEqual(question_state::$complete, $step->get_state());
        $this->assertEqual(0.5, $step->get_fraction());
        $this->assertEqual(1256233705, $step->get_timecreated());
        $this->assertEqual(1, $step->get_user_id());
        $this->assertEqual(array('-submit' => '1', 'choice0' => '1'), $step->get_all_data());

        $output = $qa->render(new question_display_options(), '1');
        $this->assertPattern('/' . preg_quote($qa->get_question()->questiontext) . '/', $output);
        $this->assertPattern('/' . preg_quote(get_string('questionusedunknownmodel', 'qbehaviour_missing')) . '/', $output);
        $this->assert(new ContainsTagWithAttribute('div', 'class', 'warning'), $output);
    }
}
