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
 * This file contains tests for the question_usage_by_activity class.
 *
 * @package moodlecore
 * @subpackage questionengine
 * @copyright © 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../lib.php');

class question_usage_by_activity_test extends UnitTestCase {

    public function test_set_get_preferred_model() {
        // Set up
        $quba = question_engine::make_questions_usage_by_activity('unit_test',
                get_context_instance(CONTEXT_SYSTEM));

        // Exercise SUT and verify.
        $quba->set_preferred_interaction_model('deferredfeedback');
        $this->assertEqual('deferredfeedback', $quba->get_preferred_interaction_model());
    }

    public function test_set_get_id() {
        // Set up
        $quba = question_engine::make_questions_usage_by_activity('unit_test',
                get_context_instance(CONTEXT_SYSTEM));

        // Exercise SUT and verify
        $quba->set_id_from_database(123);
        $this->assertEqual(123, $quba->get_id());
    }

    public function test_fake_id() {
        // Set up
        $quba = question_engine::make_questions_usage_by_activity('unit_test',
                get_context_instance(CONTEXT_SYSTEM));

        // Exercise SUT and verify
        $this->assertTrue($quba->get_id());
    }

    public function test_create_usage_and_add_question() {
        // Exercise SUT
        $context = get_context_instance(CONTEXT_SYSTEM);
        $quba = question_engine::make_questions_usage_by_activity('unit_test', $context);
        $quba->set_preferred_interaction_model('deferredfeedback');
        $tf = test_question_maker::make_a_truefalse_question();
        $qnumber = $quba->add_question($tf);

        // Verify.
        $this->assertEqual($qnumber, 1);
        $this->assertEqual('unit_test', $quba->get_owning_plugin());
        $this->assertIdentical($context, $quba->get_owning_context());
        $this->assertEqual($quba->question_count(), 1);
        $this->assertEqual($quba->get_question_state($qnumber), question_state::$notstarted);
    }

    public function test_get_question() {
        // Set up.
        $quba = question_engine::make_questions_usage_by_activity('unit_test',
                get_context_instance(CONTEXT_SYSTEM));
        $quba->set_preferred_interaction_model('deferredfeedback');
        $tf = test_question_maker::make_a_truefalse_question();
        $qnumber = $quba->add_question($tf);

        // Exercise SUT and verify.
        $this->assertIdentical($tf, $quba->get_question($qnumber));

        $this->expectException();
        $quba->get_question($qnumber + 1);
    }

    public function test_extract_responses() {
        // Start a deferred feedback attempt with CBM and add the question to it.
        $tf = test_question_maker::make_a_truefalse_question();
        $quba = question_engine::make_questions_usage_by_activity('unit_test',
                get_context_instance(CONTEXT_SYSTEM));
        $quba->set_preferred_interaction_model('deferredcbm');
        $qnumber = $quba->add_question($tf);
        $quba->start_all_questions();

        // Prepare data to be submitted
        $prefix = $quba->get_field_prefix($qnumber);
        $answername = $prefix . 'answer';
        $certaintyname = $prefix . '-certainty';
        $getdata = array(
            $answername => 1,
            $certaintyname => 3,
            'irrelevant' => 'should be ignored',
        );

        // Exercise SUT
        $submitteddata = $quba->extract_responses($qnumber, $getdata);

        // Verify.
        $this->assertEqual(array('answer' => 1, '-certainty' => 3), $submitteddata);
    }

    public function test_access_out_of_sequence_throws_exception() {
        // Start a deferred feedback attempt with CBM and add the question to it.
        $tf = test_question_maker::make_a_truefalse_question();
        $quba = question_engine::make_questions_usage_by_activity('unit_test',
                get_context_instance(CONTEXT_SYSTEM));
        $quba->set_preferred_interaction_model('deferredcbm');
        $qnumber = $quba->add_question($tf);
        $quba->start_all_questions();

        // Prepare data to be submitted
        $prefix = $quba->get_field_prefix($qnumber);
        $answername = $prefix . 'answer';
        $certaintyname = $prefix . '-certainty';
        $postdata = array(
            $answername => 1,
            $certaintyname => 3,
            $prefix . ':sequencecheck' => 1,
            'irrelevant' => 'should be ignored',
        );

        // Exercise SUT - no exception yet.
        $quba->process_all_actions($qnumber, $postdata);

        $postdata = array(
            $answername => 1,
            $certaintyname => 3,
            $prefix . ':sequencecheck' => 3,
            'irrelevant' => 'should be ignored',
        );

        // Exercise SUT - now it should fail.
        $this->expectException('question_out_of_sequence_exception');
        $quba->process_all_actions($qnumber, $postdata);
    }
}