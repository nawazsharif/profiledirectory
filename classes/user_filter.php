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
        $form->addElement('select', 'postnominals', get_string('postnominals', 'local_profile_directory'), array_column($filterdata, 'post_nominals'));
        $form->addElement('select', 'qualifications', get_string('qualifications', 'local_profile_directory'), array_column($filterdata, 'qualifications'));
        $form->addElement('select', 'specialties', get_string('specialties', 'local_profile_directory'), array_column($filterdata, 'specialties'));
        $this->add_action_buttons(false, get_string('submit', 'local_profile_directory'));
    }

}
