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
 * Select from drop down list question renderer class.
 *
 * @package qtype_sddl
 * @copyright 2010 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Generates the output for select from drop down list questions.
 *
 * @copyright 2010 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_sddl_renderer extends qtype_with_combined_feedback_renderer {
    public function formulation_and_controls(question_attempt $qa,
            question_display_options $options) {

        $question = $qa->get_question();

        $questiontext = '';
        foreach ($question->textfragments as $i => $fragment) {
            if ($i > 0) {
                $questiontext .= $this->select_box($qa, $i, $options);
            }
            $questiontext .= $fragment;
        }


        $result = '';
        $result .= html_writer::tag('div', $question->format_text($questiontext),
                array('class' => 'qtext', 'id' => $qa->get_qt_field_name('')));



        if ($qa->get_state() == question_state::$invalid) {
            $result .= html_writer::nonempty_tag('div',
                    $question->get_validation_error($qa->get_last_qt_data()),
                    array('class' => 'validationerror'));
        }

        return $result;
    }


    protected function select_box(question_attempt $qa, $place, question_display_options $options) {
        $question = $qa->get_question();
        $group = $question->places[$place];

        $fieldname = $question->field($place);

        $value = $qa->get_last_qt_var($question->field($place));

        $attributes = array(
            'id' => $this->box_id($qa, 'p' . $place, $group),
            'class' => 'group' . $group
        );

        if ($options->readonly) {
            $attributes['disabled'] = 'disabled';
        }

        $orderedchoices = $question->get_ordered_choices($group);
        $selectoptions = array();
        foreach ($orderedchoices as $orderedchoicevalue => $orderedchoice){
            $selectoptions[$orderedchoicevalue] = $orderedchoice->text;
        }

        $feedbackimage = '';
        if ($options->correctness) {
            $response = $qa->get_last_qt_data();
            if (array_key_exists($fieldname, $response)) {
                $fraction = (int) ($response[$fieldname] == $question->get_right_choice_for($place));
                $attributes['class'] = $this->feedback_class($fraction);
                $feedbackimage = $this->feedback_image($fraction);
            }
        }

        return html_writer::select($selectoptions, $qa->get_qt_field_name($fieldname), $value, ' ', $attributes) . ' ' . $feedbackimage;
    }



    protected function box_id(question_attempt $qa, $place, $group) {
        return $qa->get_qt_field_name($place) . '_' . $group;
    }

    public function specific_feedback(question_attempt $qa) {
        return $this->combined_feedback($qa);
    }

    public function correct_response(question_attempt $qa) {
        $question = $qa->get_question();

        $correctanswer = '';
        foreach ($question->textfragments as $i => $fragment) {
            if ($i > 0) {
                $group = $question->places[$i];
                $choice = $question->choices[$group][$question->rightchoices[$i]];
                $correctanswer .= '[' . str_replace('-', '&#x2011;',
                        $choice->text) . ']';
            }
            $correctanswer .= $fragment;
        }

        if (!empty($correctanswer)) {
            return get_string('correctansweris', 'qtype_sddl', $correctanswer);
        }
    }
}
