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
 * TODO describe file manage
 *
 * @package    local_profile_directory
 * @copyright  2024 Brain Station 23 <sales@brainstation-23.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
use local_profile_directory\user_filter;
require_login();
$userid = optional_param('id', 0, PARAM_INT);
$url = new moodle_url('/local/profile_directory/manage.php', []);
$PAGE->set_url($url);
$context = context_system::instance();
$PAGE->set_context($context);
//$PAGE->set_pagelayout('admin');
// $PAGE->set_title(get_string('pluginname', 'local_profile_directory'));
//$PAGE->set_heading(get_string('pluginname', 'local_profile_directory'));

$PAGE->set_primary_active_tab('siteadminnode');
$PAGE->set_secondary_active_tab('users');
$PAGE->navbar->add(get_string('userdetails', 'local_profile_directory'), $PAGE->url);

$filterdata = $DB->get_records('local_profile_directory');
$filterform = new user_filter($url, $filterdata);
$userdata = [];

if ($formdata = $filterform->get_data()) {
    $conditions = [];
    $params = [];

    if ($formdata->postnominals !== "") {
        $conditions[] = "post_nominals = :postnominals";
        $params['postnominals'] = array_column($filterdata, 'post_nominals')[$formdata->postnominals];
    }

    if ($formdata->category !== "") {
        $conditions[] = "category_id = :category";
        $params['category'] = array_column($filterdata, 'category_id')[$formdata->category];
    }

    if ($formdata->qualifications !== "") {
        $conditions[] = "qualifications = :qualification";
        $params['qualification'] = array_column($filterdata, 'qualifications')[$formdata->qualifications];
    }

    $sql = "SELECT * FROM {local_profile_directory}";
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $userdata = $DB->get_records_sql($sql, $params);

} else {
    $userdata = $DB->get_records('local_profile_directory');
}

echo $OUTPUT->header();

$context = [
    'filterform' => $filterform->render(),
    'userdata' => array_values($userdata),
];
echo $OUTPUT->render_from_template('local_profile_directory/user_management', $context);
echo $OUTPUT->footer();
