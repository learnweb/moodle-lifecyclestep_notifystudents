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
 * Lang strings for notify students step
 *
 * @package lifecyclestep_notifystudents
 * @copyright  2021 Aaron Koßler WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Studierende-benachrichtigen-Schritt';
$string['responsetimeout'] = 'Zeit, die der Lehrende hat, um zu reagieren';
$string['option'] = 'Option';
$string['option_help'] = '<p>' . 'Sie können zwischen den folgenden Optionen wählen:'
    . '<br>' . 'Opt-In: Studierende werden nicht benachrichtigt, außer wenn sich der Lehrende dafür entscheidet.'
    . '<br>' . 'Opt-Out: Studierende werden benachrichtigt, außer wenn sich der Lehrende dagegen entscheidet.'
    . '</p>';
$string['optin'] = 'Opt-In';
$string['optout'] = 'Opt-Out';
$emailplaceholders = '<p>' . 'Sie können die folgenden Platzhalter nutzen:'
    . '<br>' . 'Vorname des Empfängers: ##firstname##'
    . '<br>' . 'Nachname des Empfängers: ##lastname##'
    . '<br>' . 'Betroffene Kurse: ##courses##'
    . '</p>';
$string['teacher_subject'] = 'Betreffvorlage [Lehrende]';
$string['teacher_subject_default'] = 'Kurse werden gelöscht';
$string['teacher_subject_help'] = 'Stellen Sie die Vorlage für den Emailbetreff ein.' . $emailplaceholders;
$string['teacher_content'] = 'Email Text Vorlage [Lehrende]';
$string['teacher_content_default'] = '<p>' . 'Lieber Lehrender,'
    . '<br><br>' . 'die folgenden Kurse werden bald gelöscht:'
    . '<br>' . '##courses##'
    . '<br>' . 'Sie können darüber entscheiden, ob die Kursteilnehmer darüber informiert werden sollen oder nicht.'
    . '<br>' . 'Für weiter Informationen besuchen Sie bitte die "Kurse verwalten"-Seite.'
    . '<br><br>' . 'Mit freundlichen Grüßen'
    . '<br>' . 'Ihr Learnweb Team'
    . '</p>';
$string['teacher_content_help'] = 'Stellen Sie die Vorlage für Emails ein.' . $emailplaceholders;
$string['student_subject'] = 'Betreffvorlage [Studierende]';
$string['student_subject_default'] = 'Kurse werden gelöscht';
$string['student_subject_help'] = 'Stellen Sie die Vorlage für den Emailbetreff ein.' . $emailplaceholders;
$string['student_content'] = 'Email Text Vorlage [Studierende]';
$string['student_content_default'] = '<p>' . 'Lieber Studierender,'
    . '<br><br>' . 'die folgenden Kurse werden bald gelöscht:'
    . '<br>' . '##courses##'
    . '<br>' . 'Bitte speichern Sie alle nötigen Materialien.'
    . '<br><br>' . 'Mit freundlichen Grüßen'
    . '<br>' . 'Dein Learnweb Team'
    . '</p>';
$string['student_content_help'] = 'Stellen Sie die Vorlage für Emails ein.' . $emailplaceholders;
$string['notify'] = 'Studiernde benachrichtigen';
$string['nonotify'] = 'Studiernde nicht benachrichtigen';
$string['action_prevented_notification'] = 'Eine Benachrichtigung der Studierenden wurde von {$a} ausgeschlossen';
$string['action_accepted_notification'] = 'Eine Benachrichtigung der Studierenden wurde von {$a} eingeleitet';
$string['status_message_notify'] = 'Studierende werden nach jetzigem Stand benachrichtigt';
$string['status_message_nonotify'] = 'Studierende werden nach jetzigem Stand nicht benachrichtigt';
