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
 * Contains definition of cutsom user profile field.
 *
 * @package    profilefield_remotevalidation
 * @category   profilefield
 * @copyright  2023 Georg Maißer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
//Warning: Declaration of profile_define_remotevalidation::define_form_specific(&$form) should be compatible with
//profile_define_base::define_form_specific($form) in /var/www/html/user/profile/field/remotevalidation/define.class.php on line 34
class profile_define_remotevalidation extends profile_define_base {

    /**
     * Prints out the form snippet for the part of creating or
     * editing a profile field specific to the current data type
     *
     * @param moodleform $form reference to moodleform for adding elements.
     */
    public function define_form_specific($form) {

        // Default data.
        $form->addElement('text', 'defaultdata', get_string('profiledefaultdata', 'admin'), 'size="50"');
        $form->setType('defaultdata', PARAM_TEXT);

        // Param 4 for text type contains a link.
        $form->addElement('text', 'param3', get_string('profilefieldlink', 'admin'));
        $form->setType('param3', PARAM_URL);
        $form->addHelpButton('param3', 'profilefieldlink', 'admin');

        // Param 5 extra Regex validation - the regex pattern to be used for verifying user input.
        $form->addElement('text', 'param5', get_string('regexpattern', 'profilefield_remotevalidation'));
        $form->addHelpButton('param5', 'regexpattern', 'profilefield_remotevalidation');
        $form->setType('param5', PARAM_RAW);
    }

    /**
     * Validate the data from the add/edit profile field form
     * that is specific to the current data type
     *
     * @param object $data from the add/edit profile field form
     * @param object $files files uploaded
     * @return array associative array of error messages
     */
    public function define_validate_specific($data, $files) {
        // overwrite if necessary
        $errors = array();

        return $errors;
    }

    /**
     * Alter data before showing the form to user and fill the form with decode values.
     *
     * @param $mform
     * @return void
     */
    public function define_after_data(&$mform) {
        parent::define_after_data($mform);
        // Set the values.
        $param = $mform->getElement('param5');
        $param->setValue(base64_decode($param->getValue()));
    }

    /**
     * Process data before saving to db.
     *
     * @param $data
     * @return array|stdClass
     */
    public function define_save_preprocess($data) {
        $data = parent::define_save_preprocess($data);
        // Encode regex as regex pattern can use some nasty characters.
        $data->param5 = base64_encode($data->param5);
        return $data;
    }

}


