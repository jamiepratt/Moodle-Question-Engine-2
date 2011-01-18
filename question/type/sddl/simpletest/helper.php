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
 * Test helper class for the selection from drop down list question type.
 *
 * @package qtype_sddl
 * @copyright 2010 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_sddl_test_helper {
    /**
     * @return qtype_sddl_question
     */
    public static function make_a_sddl_question() {
        question_bank::load_question_definition_classes('sddl');
        $sddl = new qtype_sddl_question();

        test_question_maker::initialise_a_question($sddl);

        $sddl->name = 'Selection from drop down list question';
        $sddl->questiontext = 'The [[1]] brown [[2]] jumped over the [[3]] dog.';
        $sddl->generalfeedback = 'This sentence uses each letter of the alphabet.';
        $sddl->qtype = question_bank::get_qtype('sddl');

        $sddl->shufflechoices = true;

        test_question_maker::set_standard_combined_feedback_fields($sddl);

        $sddl->choices = array(
            1 => array(
                1 => new qtype_sddl_choice('quick', 1),
                2 => new qtype_sddl_choice('slow', 1)),
            2 => array(
                1 => new qtype_sddl_choice('fox', 2),
                2 => new qtype_sddl_choice('dog', 2)),
            3 => array(
                1 => new qtype_sddl_choice('lazy', 3),
                2 => new qtype_sddl_choice('assiduous', 3)),
        );

        $sddl->places = array(1 => 1, 2 => 2, 3 => 3);
        $sddl->rightchoices = array(1 => 1, 2 => 1, 3 => 1);
        $sddl->textfragments = array('The ', ' brown ', ' jumped over the ', ' dog.');

        return $sddl;
    }

    /**
     * @return qtype_sddl_question
     */
    public static function make_a_maths_sddl_question() {
        question_bank::load_question_definition_classes('sddl');
        $sddl = new qtype_sddl_question();

        test_question_maker::initialise_a_question($sddl);

        $sddl->name = 'Selection from drop down list question';
        $sddl->questiontext = 'Fill in the operators to make this equation work: ' .
                '7 [[1]] 11 [[2]] 13 [[1]] 17 [[2]] 19 = 3';
        $sddl->generalfeedback = 'This sentence uses each letter of the alphabet.';
        $sddl->qtype = question_bank::get_qtype('sddl');

        $sddl->shufflechoices = true;

        test_question_maker::set_standard_combined_feedback_fields($sddl);

        $sddl->choices = array(
            1 => array(
                1 => new qtype_sddl_choice('+', 1, true),
                2 => new qtype_sddl_choice('-', 1, true),
                3 => new qtype_sddl_choice('*', 1, true),
                4 => new qtype_sddl_choice('/', 1, true),
            ));

        $sddl->places = array(1 => 1, 2 => 1, 3 => 1, 4 => 1);
        $sddl->rightchoices = array(1 => 1, 2 => 2, 3 => 1, 4 => 2);
        $sddl->textfragments = array('7 ', ' 11 ', ' 13 ', ' 17 ', ' 19 = 3');

        return $sddl;
    }
}
