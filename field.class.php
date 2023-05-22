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
 * @package    profilefield_remotevalidation
 * @category   profilefield
 * @copyright  2023 Georg Maißer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class profile_field_remotevalidation extends profile_field_base {

    /**
     * Display the data for this field
     *
     * @return string data for custom profile field.
     */

            /**
     * Overwrite the base class to display the data for this field
     */
    public function display_data() {
        // Default formatting.
        $data = parent::display_data();

        // Are we creating a link?
        if (!empty($this->field->param4) and !empty($data)) {

            // Define the target.
            if (! empty($this->field->param5)) {
                $target = 'target="'.$this->field->param5.'"';
            } else {
                $target = '';
            }

            // Create the link.
            $icon = '<span class="media-left"><i class="icon fa fa-graduation-cap fa-fw " aria-hidden="true"></i></span>';
            $data = '<a href="'.str_replace('$$', urlencode($data), $this->field->param4).'" '.$target.'>'.$icon.htmlspecialchars($data).'</a>';
        }

        return $data;
    }



    /**
     * Adds the profile field to the moodle form class
     *
     * @param moodleform $mform instance of the moodleform class
     */
    public function edit_field_add($mform) {
        $size = 19;
        $maxlength = 19;
        $fieldtype = 'text';

        // Create the form field.
        $mform->addElement($fieldtype, $this->inputname, format_string($this->field->name), 'maxlength="'.$maxlength.'" size="'.$size.'" ');
        $mform->setType($this->inputname, PARAM_TEXT);
    }


    /**
     * Validate the form field from profile page
     *
     * @param stdClass $usernew user input
     * @return string contains error message otherwise NULL
     **/
    function edit_validate_field($usernew) {
        // overwrite if necessary
        $errors = array();


        $usernew->firstname = "xxx";

        $input_name_array = $array = get_object_vars($usernew);

        if ( !preg_match('/(\d{4}\-\d{4}\-\d{4}\-\d{3}(?:\d|X))/', $input_name_array[$this->inputname] ) ){

           // $errors[$this->inputname] = "Invalid remote validation error-".preg_last_error();
	    }

        if ($message = $this->validate($input_name_array[$this->inputname])) {
            $errors[$this->inputname] = "Validation returned the following error: " . $message;
        }

        return $errors;

    }



    /**
     * Return the field type and null properties.
     * This will be used for validating the data submitted by a user.
     *
     * @return array the param type and null property
     * @since Moodle 3.2
     */
    public function get_field_properties() {
        return array(PARAM_TEXT, NULL_NOT_ALLOWED);
    }

    /**
     * Validate via a remote server.
     *
     * @return null|string
     */
    public function validate(string $datastring) {

        if (!empty($this->field->param4)) {
            $url1 = str_replace('$$', $datastring, $this->field->param4);
        }
        if (!empty($this->field->param3)) {
            $url2 = str_replace('$$', $datastring, $this->field->param3);
        }

        $fetchagain = false;
        if (empty($url1) || !$object = self::send_request($url1)) {
            $fetchagain = true;
        } else if (!isset($object->err_msg) || $object->err_msg != "ok") {
            $fetchagain = true;
        }

        // If we have no valid result, we try with the second url.
        if (!empty($url2) && $fetchagain) {
            $object = self::send_request($url2);
        }

        if (empty($url1) && empty($url2)) {
            return get_string('noserverdefined', 'profilefield_remotevalidation');
        }

        if (!isset($object->err_msg)) {
            return get_string('problemwithserver', 'profilefield_remotevalidation');
        } else if ($object->err_msg != "ok") {
            return get_string('yourpinisinvalid', 'profilefield_remotevalidation');
        }

        return null;
    }

    /**
     * Function to effectively trigger the curl request.
     *
     * @param string $url
     * @return object
     */
    private function send_request(string $url) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        // CURLOPT_ENCODING => '',
        // CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 20,
        // CURLOPT_FOLLOWLOCATION => true,
        // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response);
    }
}

