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

namespace local_profile_directory;

defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir . '/formslib.php');
require_once(__DIR__ . '/../lib.php');
use context_user;
/**
 * Class profile_form
 *
 * @package    local_profile_directory
 * @copyright  2024 Brain Station 23 <sales@brainstation-23.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class profile_form extends \moodleform {

    protected function definition() {
        global $COURSE;
        $form = $this->_form;

        $form->addElement('header', 'profile_header', get_string('pluginname', 'local_profile_directory'));

        // Required fields marked with *
        $form->addElement('text', 'firstname', get_string('firstname'),
        ["maxlength" => "100", "size" => "30", 'placeholder' => get_string('firstname', 'local_profile_directory')]);
        $form->setType('firstname', PARAM_TEXT);
        $form->addRule('firstname', get_string('required'), 'required', null, 'client');

        $form->addElement('hidden', 'userid');
        $form->setType('userid', PARAM_TEXT);
        $form->addElement('hidden', 'courseid', $COURSE->id);
        $form->setType('courseid', PARAM_TEXT);

        $form->addElement('text', 'surname', get_string('surname',
        'local_profile_directory'), ["maxlength" => "100", "size" => "30",
        'placeholder' => get_string('surname', 'local_profile_directory')]);
        $form->setType('surname', PARAM_TEXT);
        $form->addRule('surname', get_string('required'), 'required', null, 'client');

        $form->addElement('text', 'post_nominals', get_string('postnominals',
        'local_profile_directory'), ["maxlength" => "100", "size" => "30",
        'placeholder' => get_string('postnominals', 'local_profile_directory')]);
        $form->setType('post_nominals', PARAM_TEXT);

        $form->addElement('text', 'ahpra_number', get_string('ahpra_number',
        'local_profile_directory'), ["maxlength" => "100", "size" => "30",
        'placeholder' => get_string('ahpra_number', 'local_profile_directory')]);
        $form->setType('ahpra_number', PARAM_TEXT);

        $form->addElement('text', 'other_associations', get_string('other_associations',
        'local_profile_directory'), ["maxlength" => "100", "size" => "30", 'placeholder' => get_string('other_associations', 'local_profile_directory')]);
        $form->setType('other_associations', PARAM_TEXT);

        $form->addElement('text', 'email', get_string('email'),
        ["maxlength" => "100", "size" => "30", 'placeholder' => get_string('email', 'local_profile_directory')]);
        $form->setType('email', PARAM_EMAIL);
        $form->addRule('email', get_string('required'), 'required', null, 'client');

        $form->addElement('text', 'phone', get_string('phone'),
        ["maxlength" => "100", "size" => "30", 'placeholder' => get_string('phone', 'local_profile_directory')]);
        $form->setType('phone', PARAM_TEXT);

        $form->addElement('text', 'website', get_string('website',
        'local_profile_directory'), ["maxlength" => "100", "size" => "30", 'placeholder' => get_string('website', 'local_profile_directory')]);
        $form->setType('website', PARAM_URL);

        $form->addElement('textarea', 'qualifications', get_string('qualifications',
        'local_profile_directory'), ["rows" => "5", "cols" => "50", 'placeholder' => get_string('qualifications', 'local_profile_directory')]);
        $form->setType('qualifications', PARAM_TEXT);
        $form->addRule('qualifications', get_string('required'), 'required', null, 'client');

        $specialties = array(
            0 => 'IFS',
            1 => 'MDMA',
            2 => 'Psilocybin',
            3 => 'Ayahuasca',
        );
        $form->addElement('autocomplete', 'specialties', get_string('specialties',
        'local_profile_directory'), $specialties,
        ['multiple' => true]);
        $form->setType('specialties', PARAM_TEXT);
        $form->addRule('specialties', get_string('required'), 'required', null, 'client');

        // File upload for photo/logo
        $filemanageroptions = array(
            'maxbytes' => 1048576,
            'maxfiles' => 1,
            'accepted_types' => array('image'),
        );
        $form->addElement('filemanager', 'photo', get_string('photo',
        'local_profile_directory'), null, $filemanageroptions);
        $form->addRule('photo', get_string('required'), 'required', null, 'client');

        // Category selection
        $categories = array(
            0 => 'Harm Reduction',
            1 => 'Holistic',
            2 => 'Psychedelic Integration',
            3 => 'Mindfulness',
            4 => 'Psychodynamic',
            5 => 'Psychotherapy',
        );
        $form->addElement('select', 'category_id', get_string('category'), $categories);
        $form->setType('category_id', PARAM_TEXT);

        // Add standard buttons - submit and cancel
        $this->add_action_buttons(true, get_string('submit', 'local_profile_directory'));
    }

    public function process_data($data) {
        global $DB, $USER;

        // Define context.
        $context = context_user::instance($USER->id);

        // Initialize validation errors.
        $errors = [];



        // Validate required fields.
        if (empty($data->firstname)) {
            $errors[] = get_string('error_missing_firstname', 'local_profile_directory');
        }
        if (empty($data->email) || !validate_email($data->email)) {
            $errors[] = get_string('error_invalid_email', 'local_profile_directory');
        }
        if (!empty($data->phone) && !preg_match('/^\+?[0-9]{10,15}$/', $data->phone)) {
            $errors[] = get_string('error_invalid_phone', 'local_profile_directory');
        }

        // Stop processing if there are validation errors.
        if (!empty($errors)) {
            debugging(implode("\n", $errors), DEBUG_DEVELOPER);
            return false;
        }

        // Process file upload if draft item ID is provided.
        $fileurl = null;
        if (!empty($data->photo)) {
            $draftitemid = $data->photo;

            $fileinfo = [
                'component' => 'local_profile_directory',
                'filearea' => 'images',
                'itemid' => $USER->id,
                'contextid' => $context->id,
                'filepath' => '/',
                'filename' => '' // Allow the function to generate the filename dynamically.
            ];

            // Custom function to process and generate file URL.
            $fileurl = local_profile_directory_file_urlcreate($context, $draftitemid, $fileinfo);
            if (!$fileurl) {
                debugging(get_string('error_file_upload_failed', 'local_profile_directory'), DEBUG_DEVELOPER);
                return false;
            }
        }

        // Retrieve or create a new record.
        $record = !empty($data->userid)
            ? $DB->get_record('local_profile_directory', ['userid' => $data->userid])
            : new \stdClass();

        // Populate the record.
        $record->userid = !empty($data->userid) ? $data->userid : $USER->id;
        $record->courseid = $data->courseid ?? null;
        $record->firstname = clean_param($data->firstname, PARAM_TEXT);
        $record->surname = clean_param($data->surname, PARAM_TEXT);
        $record->post_nominals = clean_param($data->post_nominals, PARAM_TEXT);
        $record->ahpra_number = clean_param($data->ahpra_number, PARAM_TEXT);
        $record->other_associations = clean_param($data->other_associations, PARAM_TEXT);
        $record->email = clean_param($data->email, PARAM_EMAIL);
        $record->phone = clean_param($data->phone, PARAM_TEXT);
        $record->website = clean_param($data->website, PARAM_URL);
        $record->qualifications = clean_param($data->qualifications, PARAM_TEXT);
        $record->specialties = !empty($data->specialties) ? json_encode($data->specialties) : null;
        $record->photourl = $fileurl;
        $record->photo = $draftitemid ?? null;
        $record->category_id = $data->category_id ?? null;
        $record->timecreated = $data->timecreated ?? time();
        $record->timemodified = time();

        try {
            // Update or insert the record.
            if (!empty($data->userid)) {
                $DB->update_record('local_profile_directory', $record);
            } else {
                $DB->insert_record('local_profile_directory', $record);
            }
        } catch (dml_exception $e) {
            debugging(get_string('error_database_operation', 'local_profile_directory') . ': ' . $e->getMessage(), DEBUG_DEVELOPER);
            return false;
        }

        return true;
    }

}
