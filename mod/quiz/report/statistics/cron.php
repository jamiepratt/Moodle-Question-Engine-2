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
 * Quiz statistics report cron code.
 *
 * @package quiz_statistics
 * @copyright 2008 Jamie Pratt
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/question/engine/compatibility.php');

/**
 * Quiz statistics report cron code. Deletes cached data more than a certain age.
 */
function quiz_report_statistics_cron() {

    $expiretime = time() - 5*HOURSECS;
    $todelete = get_records_select_menu('quiz_statistics', "timemodified < $expiretime");

    if (!$todelete) {
        return true;
    }

    list($todeletesql, $todeleteparams) = get_in_or_equal(array_keys($todelete));

    if (!delete_records_select('quiz_statistics', "id $todeletesql")) {
        mtrace('Error deleting out of date quiz_statistics records.');
    }

    if (!delete_records_select('quiz_question_statistics', "quizstatisticsid $todeletesql")){
        mtrace('Error deleting out of date quiz_question_statistics records.');
    }

    if (!delete_records_select('quiz_question_response_stats', "quizstatisticsid $todeletesql")) {
        mtrace('Error deleting out of date quiz_question_response_stats records.');
    }

    return true;
}
