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
use MoodleQuickForm;
use moodleform;
/**
 * Class user_filter
 *
 * @package    local_profile_directory
 * @copyright  2024 Brain Station 23 <sales@brainstation-23.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_filter extends moodleform {

    public function definition() {
        global $CFG;
        $form = $this->_form;
        $filterdata = $this->_customdata;
        $elements = array();
        // $speciality = array_merge(...array_map(function ($data) {
        // return (array)json_decode($data);
        // }, array_column($filterdata, 'specialties')));

        // Adding label and select elements for Postnominals
        $elements[] = $form->createElement('html', '<div class="d-flex flex-wrap justify-content-between align-items-center w-100">');
        $elements[] = $form->createElement('html', '<div class="form-group">');
        $elements[] = $form->createElement('static', 'postnominals_label', '', get_string('postnominals', 'local_profile_directory'));
        $elements[] = $form->createElement('select', 'postnominals', '', array_merge(['' => 'Select'], array_column($filterdata, 'post_nominals')));
        $elements[] = $form->createElement('html', '</div>'); // Close the div for Postnominals

        // Adding label and select elements for Qualifications
        $elements[] = $form->createElement('html', '<div class="form-group">');
        $elements[] = $form->createElement('static', 'qualifications_label', '', get_string('qualifications', 'local_profile_directory'));
        $elements[] = $form->createElement('select', 'qualifications', '', array_merge(['' => 'Select'], array_column($filterdata, 'qualifications')));
        $elements[] = $form->createElement('html', '</div>'); // Close the div for Qualifications

        // Adding label and select elements for Specialties
        $elements[] = $form->createElement('html', '<div class="form-group">');
        $elements[] = $form->createElement('static', 'specialties_label', '', get_string('category', 'local_profile_directory'));
        $elements[] = $form->createElement('select', 'category', '', array_merge(['' => 'Select'], array_column($filterdata, 'category_id')));
        $elements[] = $form->createElement('html', '</div>'); // Close the div for Specialties
        $elements[] = $form->createElement('html', '</div>'); // Close the div for Specialties

        // Adding all elements to the form in a group
        $form->addGroup($elements, 'filter_group', '', array(' '), false);

        // Add submit button with the correct label
        $this->add_action_buttons(true, get_string('submit', 'local_profile_directory'));

    }
}
