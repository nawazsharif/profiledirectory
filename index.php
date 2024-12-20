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
 * TODO describe file index
 *
 * @package    local_profile_directory
 * @copyright  2024 Brain Station 23 <sales@brainstation-23.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
use local_profile_directory\profile_form;
global $USER;
$courseid = optional_param('id', $USER->id, PARAM_INT);
$userid = optional_param('user', 0, PARAM_INT);
require_login($courseid);
$url = new moodle_url('/local/profile_directory/index.php', ['id' => $courseid]);
$PAGE->set_url($url);
$context = context_course::instance($courseid);
$PAGE->set_context($context);

$form = new profile_form(new moodle_url('/local/profile_directory/index.php', ['id' => $courseid]));

if ($form->is_cancelled()) {
    redirect(new moodle_url('/course/view.php', ['id' => $courseid]));
} else if ($data = $form->get_data()) {
    $form->process_data($data);
    if (isset($data->userid) && is_siteadmin()) {
        //redirect(new moodle_url('/local/profile_directory/manage.php'),
        redirect(new moodle_url('/local/profile_directory/index.php', ['id' => $courseid]),
            "Data updated Successfully", 500, \core\output\notification::NOTIFY_SUCCESS);
    } else {
        redirect(new moodle_url('/course/view.php', ['id' => $courseid]),
        "Data saved Success", 500, \core\output\notification::NOTIFY_INFO);
    }

}

echo $OUTPUT->header();
$userdata = $DB->get_record('local_profile_directory', ['userid' => is_siteadmin() && $userid ? $userid : $USER->id]);
if ($userdata) {
    $userdata->specialties = json_decode($userdata->specialties);
    $form->set_data($userdata);
}
$form->display();

echo $OUTPUT->footer();
