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
     * Adds the profile field to the moodle form class
     *
     * @param moodleform $mform instance of the moodleform class
     */
    public function edit_field_add($mform) {
        global $PAGE;
        $size = 19;
        $maxlength = 19;
        $fieldtype = 'text';
        $isadmin = 0;
        if (is_siteadmin()) {
            $isadmin = 1;
        }
        $PAGE->requires->js_call_amd('profilefield_remotevalidation/checkotherfield', 'init', [$isadmin]);

        // Create the form field.
        $mform->addElement($fieldtype, $this->inputname, format_string($this->field->name), 'maxlength="'.$maxlength.'" size="'.$size.'" ');
        $mform->setType($this->inputname, PARAM_TEXT);
    }


    /**
     * Validate the form field from profile page
     *
     * @param stdClass $usernew user input
     * @return array contains error message otherwise NULL
     **/
    function edit_validate_field($usernew): array {
        // Overwrite if necessary.
        $errors = parent::edit_validate_field($usernew);
        if (!empty($errors)) {
            return $errors;
        }
        $input_name_array = get_object_vars($usernew);

        // Extrea required check as this does not seem to work in the parent method.
        if (empty($input_name_array[$this->inputname]) && !$this->is_required()) {
            return $errors;
        }
        if (empty($input_name_array[$this->inputname]) && $this->is_required()) {
            $errors[$this->inputname] = get_string('err_required', 'core_form');
            return $errors;
        }

        if ($message = $this->validate("{$input_name_array[$this->inputname]}")) {
            $errors[$this->inputname] = get_string('validationerror', 'profilefield_remotevalidation') .
                    format_string($message);
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
    public function get_field_properties(): array {
        return array(PARAM_ALPHANUM, NULL_NOT_ALLOWED);
    }

    public function edit_save_data_preprocess($data, $datarecord) {
        global $DB;
        // Conditional creation of a fake unique number.
        if ($DB->record_exists('user_info_field', ['datatype' => 'conditional']) && strtolower($data) === "nopin") {
            $datestring = date('dmY');
            $uniquenumber = str_pad(mt_rand(0, 99999), 4, '0', STR_PAD_LEFT);
            $data = "9" . $datestring . $uniquenumber;
        }
        return parent::edit_save_data_preprocess($data, $datarecord);
    }

    /**
     * Validate via a remote server.
     * Returns null if the data could be validated. If not a string with the error messsage is returned.
     *
     * @param string $datastring
     * @return ?string error message if an error occurs
     */
    public function validate(string $datastring): ?string {
        global $DB, $USER;
        // First validate if the input matches the regex pattern. Get config for pattern validation:
        $pattern = base64_decode($this->field->param5);
        // Special validation for KSMI start.
        if ($DB->record_exists('user_info_field', ['datatype' => 'conditional']) && strtolower($datastring) === "nopin") {
            return null;
        }
        if ($DB->record_exists('user_info_field', ['datatype' => 'conditional']) &&
                preg_match("/^(9)(0[1-9]|[12][0-9]|3[01])(0[1-9]|1[012])(19|20)\d{7}$/", $datastring)) {
            return null;
        }
        // Check if PIN is already used for another user.
        $shortname = $this->get_shortname();
        // SQL query to check if the value is already set for another user
        $sql = "
            SELECT COUNT(*)
            FROM {user_info_data} uid
            JOIN {user_info_field} uif ON uid.fieldid = uif.id
            WHERE uif.shortname = :shortname
            AND uid.data = :newvalue
            AND uid.userid <> :currentuserid
        ";

        $params = [
                'shortname' => $shortname,
                'newvalue' => $this->field->param3,
                'currentuserid' => $USER->id,
        ];

        $count = $DB->count_records_sql($sql, $params);

        // Check if count is zero, indicating the value is unique
        if ($count === 0) {
            // The value is unique. Do nothing.
        } else {
            // The value is not unique, that means a user already exists using that PIN.
            $pwrecovery = html_writer::link(
                    new moodle_url('/login/forgot_password.php'),
                    get_string('passwordrecovery', 'core')
            );
            return get_string('valuealreadyset', 'profilefield_remotevalidation', $pwrecovery);;
        }
        // Special validation KSMI end.
        
        if (!empty($this->field->param5) and !empty($datastring)) {
            if ( !preg_match("/{$pattern}/", $datastring) ){
                return get_string('wrongpattern', 'profilefield_remotevalidation');
            }
        }
        
        if (!empty($this->field->param3)) {
            $url = str_replace('$$', $datastring, $this->field->param3);
            $response = self::send_request($url);
        } else {
            return get_string('noserverdefined', 'profilefield_remotevalidation');
        }

        if (!empty($response) && !isset($response->err) && $response->err_msg == "ok") {
            return null;
        }
        if (empty($response)) {
            return get_string('problemwithserver', 'profilefield_remotevalidation');
        }
        if (isset($response->err) && !empty($response->ru_message)) {
            return $response->ru_message . " / " . $response->en_message;
        }
        return null;
    }

    /**
     * Function to effectively trigger the curl request.
     *
     * @param string $url
     * @return object
     */
    private function send_request(string $url): ?object {
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $error = json_last_error();
        if(empty($response) || $error !== JSON_ERROR_NONE) {
            $return = new stdClass();
            $return->err_msg = "" . $response;
            return $return;
        } else {
            $return = json_decode($response, false);
        }
        return $return;
    }
}

