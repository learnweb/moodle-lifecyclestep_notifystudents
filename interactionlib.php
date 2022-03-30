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
 * Implementation for the interactions of the notifystudents step.
 *
 * @package lifecyclestep_notifystudents
 * @copyright  2021 Aaron Koßler WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lifecycle\step;

use context_course;
use tool_lifecycle\local\entity\process;
use tool_lifecycle\local\entity\step_subplugin;
use tool_lifecycle\local\manager\process_data_manager;
use tool_lifecycle\local\manager\process_manager;
use tool_lifecycle\local\manager\settings_manager;
use tool_lifecycle\local\manager\step_manager;
use tool_lifecycle\local\response\step_interactive_response;
use tool_lifecycle\settings_type;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../interactionlib.php');
require_once(__DIR__ . '/lib.php');

/**
 * Implementation for the interactions of the notifystudents step.
 *
 * @package lifecyclestep_notifystudents
 * @copyright  2021 Aaron Koßler WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class interactionnotifystudents extends interactionlibbase {

    /** @var string Action string for triggering to notify students. */
    const ACTION_NOTIFY = 'Notify';
    /** @var string Action string for triggering to not notify students. */
    const ACTION_NONOTIFY = 'Unnotify';

    /**
     * Returns the capability a user has to have to make decisions for a specific course.
     * @return string capability string.
     */
    public function get_relevant_capability() {
        return 'lifecyclestep/notifystudents:choice';
    }

    /**
     * Returns an array of interaction tools to be displayed on the view.php
     * Every entry is itself an array which consist of three elements:
     *  'action' => an action string, which is later passed to handle_action
     *  'alt' => a string text of the button
     * @param process $process process the action tools are requested for
     * @return array of action tools
     * @throws \coding_exception
     */
    public function get_action_tools($process) {
        $step = step_manager::get_step_instance_by_workflow_index($process->workflowid, $process->stepindex);
        $settings = settings_manager::get_settings($step->id, settings_type::STEP);
        if ($settings['option'] == notifystudents::OPTIN) {
            return array(
                array('action' => self::ACTION_NOTIFY,
                    'alt' => get_string('notify', 'lifecyclestep_notifystudents'),
                ),
                array('action' => self::ACTION_NONOTIFY,
                    'alt' => get_string('nonotify', 'lifecyclestep_notifystudents'),
                ),
            );
        } else if ($settings['option'] == notifystudents::OPTOUT) {
            return array(
                array('action' => self::ACTION_NOTIFY,
                    'alt' => get_string('notify', 'lifecyclestep_notifystudents'),
                ),
                array('action' => self::ACTION_NONOTIFY,
                    'alt' => get_string('nonotify', 'lifecyclestep_notifystudents'),
                ),
            );
        }
    }

    /**
     * Returns the status message for the given process.
     * @param process $process process the status message is requested for
     * @return string status message
     * @throws \coding_exception
     */
    public function get_status_message($process) {
        $step = step_manager::get_step_instance_by_workflow_index($process->workflowid, $process->stepindex);
        $settings = settings_manager::get_settings($step->id, settings_type::STEP);
        if ($settings['option'] == notifystudents::OPTIN) {
            return get_string('status_message_nonotify', 'lifecyclestep_notifystudents');
        } else if ($settings['option'] == notifystudents::OPTOUT) {
            return get_string('status_message_notify', 'lifecyclestep_notifystudents');
        }
    }

    /**
     * Called when a user triggered an action for a process instance.
     * @param process $process instance of the process the action was triggered upon.
     * @param step_subplugin $step instance of the step the process is currently in.
     * @param string $action action string
     * @return step_interactive_response defines if the step still wants to process this course
     *      - proceed: the step has finished and respective controller class can take over.
     *      - stillprocessing: the step still wants to process the course and is responsible for rendering the site.
     *      - noaction: the action is not defined for the step.
     *      - rollback: the step has finished and respective controller class should rollback the process.
     */
    public function handle_interaction($process, $step, $action = 'default') {
        global $DB;
        $type = 'student';
        $typeid = notifystudents::STUDENT;
        if ($action == self::ACTION_NOTIFY) {
            notifystudents::send_email($type, $process->courseid);
            return step_interactive_response::proceed();
        } else if ($action == self::ACTION_NONOTIFY) {
            $DB->delete_records('lifecyclestep_notifystudents',
                array('instanceid' => $step->id, 'courseid' => $process->courseid, 'emailtype' => $typeid));
            return step_interactive_response::proceed();
        }
        return step_interactive_response::no_action();
    }

    /**
     * Returns the due date.
     * @param int $processid Id of the process.
     * @param int $stepid Id of the step instance.
     * @return string formatted date.
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function get_due_date($processid, $stepid) {
        $process = process_manager::get_process_by_id($processid);
        $date = $process->timestepchanged;
        $settings = settings_manager::get_settings($stepid, settings_type::STEP);
        $date += $settings['responsetimeout'];
        return date('d.m.Y', $date);
    }

    /**
     * Returns the display name for the given action.
     * Used for the past actions table in view.php.
     * @param string $action Identifier of action
     * @param string $user html-link with username as text that refers to the user profile.
     * @return string action display name
     * @throws \coding_exception
     */
    public function get_action_string($action, $user) {
        if ($action == self::ACTION_NOTIFY) {
            return get_string('action_accepted_notification', 'lifecyclestep_notifystudents', $user);
        } else if ($action == self::ACTION_NONOTIFY) {
            return get_string('action_prevented_notification', 'lifecyclestep_notifystudents', $user);
        }
    }
}
