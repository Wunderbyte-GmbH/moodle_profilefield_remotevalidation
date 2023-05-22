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
    function define_form_specific($form) {

        // Default data.
        $form->addElement('text', 'defaultdata', get_string('profiledefaultdata', 'admin'), 'size="50"');
        $form->setType('defaultdata', PARAM_TEXT);

        // Param 4 for text type contains a link.
        $form->addElement('text', 'param4', get_string('profilefieldlink', 'admin'));
        $form->setType('param4', PARAM_URL);
        $form->addHelpButton('param4', 'profilefieldlink', 'admin');

        // Param 4 for text type contains a link.
        $form->addElement('text', 'param3', get_string('profilefieldlink', 'admin'));
        $form->setType('param3', PARAM_URL);
        $form->addHelpButton('param3', 'profilefieldlink', 'admin');

        // Param 5 for text type contains link target.
	    // Include the target in case you want it to link to another uri
        $targetoptions = array( ''       => get_string('linktargetnone', 'editor'),
                                '_blank' => get_string('linktargetblank', 'editor'),
                                '_self'  => get_string('linktargetself', 'editor'),
                                '_top'   => get_string('linktargettop', 'editor')
                              );
        $form->addElement('select', 'param5', get_string('profilefieldlinktarget', 'admin'), $targetoptions);
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
    function define_validate_specific($data, $files) {
        // overwrite if necessary
        $errors = array();

        return $errors;
    }

}


