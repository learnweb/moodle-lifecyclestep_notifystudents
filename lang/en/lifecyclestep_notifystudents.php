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
 * Lang strings for delete course step
 *
 * @package lifecyclestep_notifystudents
 * @copyright  2021 Aaron Koßler WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Notify students step';
$string['responsetimeout'] = 'Response Timeout';
$string['option'] = 'Option';
$string['optin'] = 'Opt-In';
$string['optout'] = 'Opt-Out';
$emailplaceholders = '<p>' . 'You can use the following placeholders:'
    . '<br>' . 'First name of recipient: ##firstname##'
    . '<br>' . 'Last name of recipient: ##lastname##'
    . '<br>' . 'Impacted courses: ##courses-html##'
    . '</p>';
$string['teacher_subject'] = 'Email Title [Teachers]';
$string['teacher_subject_default'] = 'Courses are being deleted';
$string['teacher_subject_help'] = $emailplaceholders;
$string['teacher_content'] = 'Email Text [Teachers]';
$string['teacher_content_default'] = '<p>' . 'Dear Teacher,'
    . '<br><br>' . 'the following courses are being deleted:'
    . '<br>' . '##courses##'
    . '<br>' . 'It is for you to decide if the students get notified or not.'
    . '<br>' . 'For further details please visit the "Manage Courses" page.'
    . '<br><br>' . 'Best Regards'
    . '<br>' . 'Your Learnweb Team'
    . '</p>';
$string['teacher_content_help'] = $emailplaceholders;
$string['student_subject'] = 'Email Title [Students]';
$string['student_subject_default'] = 'Courses are being deleted';
$string['student_subject_help'] = $emailplaceholders;
$string['student_content'] = 'Email Text [Students]';
$string['student_content_default'] = '<p>' . 'Dear Student,'
    . '<br><br>' . 'the following courses are being deleted:'
    . '<br>' . '##courses##'
    . '<br>' . 'Please save all necessary material before deletion.'
    . '<br><br>' . 'Best Regards'
    . '<br>' . 'Your Learnweb Team'
    . '</p>';
$string['student_content_help'] = $emailplaceholders;
$string['notify'] = 'Notify Students';
$string['nonotify'] = 'Do not notify Students';

