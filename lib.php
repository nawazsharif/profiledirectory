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
 * Callback implementations for Profile directory
 *
 * @package    local_profile_directory
 * @copyright  2024 Brain Station 23 <sales@brainstation-23.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function local_profile_directory_get_path_from_pluginfile(array $args): array {
    // Cursive never has an itemid (the number represents the revision but it's not stored in database).
    array_shift($args);

    // Get the filepath.
    if (empty($args)) {
        $filepath = '/';
    } else {
        $filepath = '/' . implode('/', $args) . '/';
    }

    return [
        'itemid' => 0,
        'filepath' => $filepath,
    ];
}

function local_profile_directory_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload) {
    global $CFG;

    require_once($CFG->libdir . '/filelib.php');

    // We are positioning the elements.
    if ($filearea === 'images') {
        // if ($context->contextlevel == CONTEXT_MODULE) {
        // require_login($course, false, $cm);
        // } else if ($context->contextlevel == CONTEXT_SYSTEM && !has_capability('mod/wordninja:addlanguage', $context)) {
        // return false;
        // }

        $relativepath = implode('/', $args);
        $fullpath = '/' . $context->id . '/local_profile_directory/images/' . $relativepath;

        $fs = get_file_storage();
        $file = $fs->get_file_by_hash(sha1($fullpath));
        if (!$file || $file->is_directory()) {
            return false;
        }

        send_stored_file($file, 0, 0, $forcedownload);
    }
}

function local_profile_directory_file_urlcreate($context, $draftitemid, $fileinfo) {
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'user', 'draft', $draftitemid, 'itemid', false);

    foreach ($files as $file) {

        if ($file->get_filename() != '.') {
            $fileinfo['filename'] = $file->get_filename();

            try {
                // Save the file in Moodle's file system.
                $fs->create_file_from_storedfile($fileinfo, $file);
            } catch (stored_file_creation_exception $e) {
                debugging('Error creating file: ' . $e->getMessage());
            }

            $fileurl = moodle_url::make_pluginfile_url(
                $fileinfo['contextid'],
                $fileinfo['component'],
                $fileinfo['filearea'],
                $fileinfo['itemid'],
                $fileinfo['filepath'],
                $fileinfo['filename'],
                true,
            );
            // Display the image.
            $downloadurl = $fileurl->get_port() ?
                $fileurl->get_scheme() . '://' . $fileurl->get_host() . ':' . $fileurl->get_port() . $fileurl->get_path() :
                $fileurl->get_scheme() . '://' . $fileurl->get_host() . $fileurl->get_path();

            return $downloadurl;
        }
    }
    return false;
}

function local_profile_directory_extend_navigation_course(\navigation_node $navigation, \stdClass $course) {
    global $CFG, $USER, $DB;
    require_once(__DIR__ . "/locallib.php");

    $url = new moodle_url($CFG->wwwroot . '/local/profile_directory/index.php', ['id' => $course->id]);

    $navigation->add(
        get_string('pluginname', 'local_profile_directory'),
        $url,
        navigation_node::TYPE_SETTING,
        null,
        null,
        new pix_icon('i/report', ''),
    );

}
