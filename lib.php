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
 * Step subplugin to notify students of a course that the course is being deleted.
 *
 * @package    lifecyclestep_notifystudents
 * @copyright  2021 Aaron Koßler WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lifecycle\step;

use context_course;
use core_user;
use tool_lifecycle\local\manager\process_manager;
use tool_lifecycle\local\manager\settings_manager;
use tool_lifecycle\local\manager\step_manager;
use tool_lifecycle\local\response\step_response;
use tool_lifecycle\settings_type;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lib.php');

/**
 * Step subplugin to notify students of a course that the course is being deleted.
 *
 * @package    lifecyclestep_notifystudents
 * @copyright  2021 Aaron Koßler WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class notifystudents extends libbase {

    /** @var int Integer for teacher email type. */
    const TEACHER = 0;
    /** @var int Integer for student email type. */
    const STUDENT = 1;
    /** @var int Integer for opt-out option. */
    const OPTOUT = 0;
    /** @var int Integer for opt-in option. */
    const OPTIN = 1;

    /**
     * Processes the course and returns a response.
     * The response tells either
     *  - that the subplugin is finished processing.
     *  - that the subplugin is not yet finished processing.
     *  - that a rollback for this course is necessary.
     * @param int $processid of the respective process.
     * @param int $instanceid of the step instance.
     * @param mixed $course to be processed.
     * @return step_response
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function process_course($processid, $instanceid, $course) {
        global $DB;
        $context = context_course::instance($course->id);
        $userrecords = get_users_by_capability($context, 'lifecyclestep/notifystudents:choice');
        foreach ($userrecords as $userrecord) {
            $record = new \stdClass();
            $record->touser = $userrecord->id;
            $record->courseid = $course->id;
            $record->instanceid = $instanceid;
            $record->emailtype = self::TEACHER;
            $DB->insert_record('lifecyclestep_notifystudents', $record);
        }
        $userrecords = get_users_by_capability($context, 'lifecyclestep/notifystudents:students');
        foreach ($userrecords as $userrecord) {
            $record = new \stdClass();
            $record->touser = $userrecord->id;
            $record->courseid = $course->id;
            $record->instanceid = $instanceid;
            $record->emailtype = self::STUDENT;
            $DB->insert_record('lifecyclestep_notifystudents', $record);
        }
        return step_response::waiting();
    }

    /**
     * Processes the course in status waiting and returns a response.
     * The response tells either
     *  - that the subplugin is finished processing.
     *  - that the subplugin is not yet finished processing.
     *  - that a rollback for this course is necessary.
     * @param int $processid of the respective process.
     * @param int $instanceid of the step instance.
     * @param mixed $course to be processed.
     * @return step_response
     */
    public function process_waiting_course($processid, $instanceid, $course) {
        global $DB;
        // What happens when time runs up.
        $settings = settings_manager::get_settings($instanceid, settings_type::STEP);
        $process = process_manager::get_process_by_id($processid);
        if ($process->timestepchanged < time() - $settings['responsetimeout']) {
            if ($settings['option'] == self::OPTOUT) {
                // What happens if opt-out was chosen.
                $type = 'student';
                self::send_email($type);
            } else if ($settings['option'] == self::OPTIN) {
                // What happens if opt-in was chosen.
                $DB->delete_records('lifecyclestep_notifystudents',
                    array('instanceid' => $instanceid, 'courseid' => $course->id, 'emailtype' => self::STUDENT));
            }
            return step_response::proceed();
        }
        return step_response::waiting();
    }

    /**
     * Send emails to all teachers, but only one mail per teacher.
     */
    public function post_processing_bulk_operation() {
        $type = 'teacher';
        self::send_email($type);
    }

    /**
     * Send emails depending on type (teacher or student).
     * @param string $type of receiver.
     * @param int $courseid optional parameter for courseid.
     */
    public static function send_email($type, $courseid = null) {
        global $DB, $PAGE;
        if ($type == 'teacher') {
            $typeid = self::TEACHER;
        } else if ($type == 'student') {
            $typeid = self::STUDENT;
        }
        $stepinstances = step_manager::get_step_instances_by_subpluginname((new notifystudents())->get_subpluginname());
        foreach ($stepinstances as $step) {
            $settings = settings_manager::get_settings($step->id, settings_type::STEP);
            // Set system context, since format_text needs a context.
            $PAGE->set_context(\context_system::instance());
            // Format the raw string in the DB to FORMAT_HTML.
            $settings[$type . '_content'] = format_text($settings[$type . '_content'], FORMAT_HTML);
            if ($courseid) {
                $userstobeinformed = $DB->get_records('lifecyclestep_notifystudents',
                    array('courseid' => $courseid, 'instanceid' => $step->id, 'emailtype' => $typeid), '', 'distinct touser');
            } else {
                $userstobeinformed = $DB->get_records('lifecyclestep_notifystudents',
                    array('instanceid' => $step->id, 'emailtype' => $typeid), '', 'distinct touser');
            }
            foreach ($userstobeinformed as $userrecord) {
                $user = \core_user::get_user($userrecord->touser);
                $transaction = $DB->start_delegated_transaction();
                if ($courseid) {
                    $mailentries = $DB->get_records('lifecyclestep_notifystudents',
                        array('instanceid' => $step->id, 'courseid' => $courseid,
                            'touser' => $user->id, 'emailtype' => $typeid));
                } else {
                    $mailentries = $DB->get_records('lifecyclestep_notifystudents',
                        array('instanceid' => $step->id, 'touser' => $user->id, 'emailtype' => $typeid));
                }
                $parsedsettings = self::replace_placeholders($settings, $user, $step->id, $mailentries);
                $subject = $parsedsettings[$type . '_subject'];
                $contenthtml = $parsedsettings[$type . '_content'];
                email_to_user($user, \core_user::get_noreply_user(), $subject, html_to_text($contenthtml), $contenthtml);
                if ($courseid) {
                    $DB->delete_records('lifecyclestep_notifystudents',
                        array('instanceid' => $step->id, 'courseid' => $courseid,
                            'touser' => $user->id, 'emailtype' => $typeid));
                } else {
                    $DB->delete_records('lifecyclestep_notifystudents',
                        array('instanceid' => $step->id, 'touser' => $user->id, 'emailtype' => $typeid));
                }
                $transaction->allow_commit();
            }
        }
    }

    /**
     * Replaces certain placeholders within the mail template.
     * @param string[] $strings array of mail templates.
     * @param core_user $user User object.
     * @param int $stepid Id of the step instance.
     * @param array[] $mailentries Array consisting of course entries from the database.
     * @return string[] array of mail text.
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public static function replace_placeholders($strings, $user, $stepid, $mailentries) {

        $patterns = array();
        $replacements = array();

        // Replaces firstname of the user.
        $patterns [] = '##firstname##';
        $replacements [] = $user->firstname;

        // Replaces lastname of the user.
        $patterns [] = '##lastname##';
        $replacements [] = $user->lastname;

        // Replace courses html.
        $patterns [] = '##courses##';
        $courses = $mailentries;
        $coursestabledata = array();
        foreach ($courses as $entry) {
            $coursestabledata[$entry->courseid] = self::parse_course_row_data($entry->courseid);
        }
        $coursestable = new \html_table();
        $coursestable->data = $coursestabledata;
        $replacements [] = \html_writer::table($coursestable);

        return str_ireplace($patterns, $replacements, $strings);
    }

    /**
     * Parses a course for the html format.
     * @param int $courseid id of the course
     * @return array column of a course
     * @throws \dml_exception
     */
    private static function parse_course_row_data($courseid) {
        $course = get_course($courseid);
        return array($course->fullname);
    }

    /**
     * The return value should be equivalent with the name of the subplugin folder.
     * @return string technical name of the subplugin
     */
    public function get_subpluginname() {
        return 'notifystudents';
    }

    /**
     * Defines which settings each instance of the subplugin offers for the user to define.
     * @return instance_setting[] containing settings keys and PARAM_TYPES
     */
    public function instance_settings() {
        return array(
            new instance_setting('responsetimeout', PARAM_INT),
            new instance_setting('option', PARAM_INT),
            new instance_setting('teacher_subject', PARAM_TEXT),
            new instance_setting('teacher_content', PARAM_RAW),
            new instance_setting('student_subject', PARAM_TEXT),
            new instance_setting('student_content', PARAM_RAW),
        );
    }

    /**
     * This method can be overriden, to add form elements to the form_step_instance.
     * It is called in definition().
     * @param \MoodleQuickForm $mform
     * @throws \coding_exception
     */
    public function extend_add_instance_form_definition($mform) {

        // Adding a time limit for the teacher to respond.
        $elementname = 'responsetimeout';
        $mform->addElement('duration', $elementname, get_string('responsetimeout', 'lifecyclestep_notifystudents'));
        $mform->setType($elementname, PARAM_INT);

        // Adding radio buttons for opt-in or opt-out.
        $elementname = 'option';
        $radioarray = array();
        $radioarray[] = $mform->createElement('radio', $elementname, '', get_string('optin', 'lifecyclestep_notifystudents'), 1);
        $radioarray[] = $mform->createElement('radio', $elementname, '', get_string('optout', 'lifecyclestep_notifystudents'), 0);
        $mform->addGroup($radioarray, 'opt', get_string('option', 'lifecyclestep_notifystudents'), array(' '), false);
        $mform->addHelpButton('opt', 'option', 'lifecyclestep_notifystudents');

        // Adding a subject field for the email to the editingteachers.
        $elementname = 'teacher_subject';
        $mform->addElement('textarea', $elementname, get_string('teacher_subject', 'lifecyclestep_notifystudents'),
            array('style="resize:none" wrap="virtual" rows="1" cols="100"'));
        $mform->addHelpButton($elementname, 'teacher_subject', 'lifecyclestep_notifystudents');
        $mform->setType($elementname, PARAM_TEXT);
        $mform->setDefault($elementname, get_string('teacher_subject_default', 'lifecyclestep_notifystudents'));

        // Adding a content field for the email to the editing teachers.
        $elementname = 'teacher_content';
        $mform->addElement('editor', $elementname, get_string('teacher_content', 'lifecyclestep_notifystudents'))
            ->setValue(array('text' => get_string('teacher_content_default', 'lifecyclestep_notifystudents')));
        $mform->addHelpButton($elementname, 'teacher_content', 'lifecyclestep_notifystudents');
        $mform->setType($elementname, PARAM_RAW);

        // Adding a subject field for the email to the students.
        $elementname = 'student_subject';
        $mform->addElement('textarea', $elementname, get_string('student_subject', 'lifecyclestep_notifystudents'),
            array('style="resize:none" wrap="virtual" rows="1" cols="100"'));
        $mform->addHelpButton($elementname, 'student_subject', 'lifecyclestep_notifystudents');
        $mform->setType($elementname, PARAM_TEXT);
        $mform->setDefault($elementname, get_string('student_subject_default', 'lifecyclestep_notifystudents'));

        // Adding a content field for the email to the students.
        $elementname = 'student_content';
        $mform->addElement('editor', $elementname, get_string('student_content', 'lifecyclestep_notifystudents'))
            ->setValue(array('text' => get_string('student_content_default', 'lifecyclestep_notifystudents')));
        $mform->addHelpButton($elementname, 'student_content', 'lifecyclestep_notifystudents');
        $mform->setType($elementname, PARAM_RAW);

    }
}
