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
 * Profile directory view
 *
 * @package    local_profile_directory
 * @copyright  2024 Brain Station 23 <sales@brainstation-23.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

require_login();

// Required parameter.
$userid = required_param('user', PARAM_INT);

// Define category and specialties mappings.
$categories = [
    0 => 'Harm Reduction',
    1 => 'Holistic',
    2 => 'Psychedelic Integration',
    3 => 'Mindfulness',
    4 => 'Psychodynamic',
    5 => 'Psychotherapy',
];

$specialties_map = [
    0 => 'IFS',
    1 => 'MDMA',
    2 => 'Psilocybin',
    3 => 'Ayahuasca',
];

// Set up the page.
$url = new moodle_url('/local/profile_directory/view.php', []);
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());
$PAGE->set_secondary_active_tab('users');
echo $OUTPUT->header();

// Fetch user data from the database.
$data = $DB->get_record('local_profile_directory', ['userid' => $userid], '*', MUST_EXIST);

// Convert category ID to its corresponding name.
$data->category_name = $categories[$data->category_id] ?? 'Unknown';

// Convert specialties JSON to a readable list of names.
$specialties_ids = json_decode($data->specialties, true);
$data->specialties_names = [];
if (!empty($specialties_ids) && is_array($specialties_ids)) {
    foreach ($specialties_ids as $id) {
        if (isset($specialties_map[$id])) {
            $data->specialties_names[] = $specialties_map[$id];
        }
    }
}
$data->specialties_names = implode(', ', $data->specialties_names);

// Render the template.
echo $OUTPUT->render_from_template('local_profile_directory/profile_view', ['userdata' => $data]);
echo $OUTPUT->footer();
