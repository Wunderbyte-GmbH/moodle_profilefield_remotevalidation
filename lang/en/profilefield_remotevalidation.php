<?php

/**
 * Contains language strings
 *
 * @package    profilefield_remotevalidation
 * @category   profilefield
 * @copyright  2023 Georg Maißer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
//name of the plugin should be defined.
$string['pluginname'] = 'Remote Validation';
$string['privacy:metadata'] = 'The Remote Validation custom profile field type is just an extension of the core user profile fields. It does not share data with any external platform.';

$string['noserverdefined'] = 'No server is defined for remote validation. Tell your admin.';
$string['problemwithserver'] = 'There is an invalid response from your defined server.';
$string['yourpinisinvalid'] = 'Your data for {$a} seems to be invalid. Please try again.';
$string['regexpattern'] = 'Enter the RegEx pattern for performing a first validation of the PIN. This is done before verifying it remotely.';
$string['regexpattern_help'] = 'Enter a Regex Pattern here like you would to it on https://regex101.com/ . Example for checking a number
from 9 to 12 digits this would be the pattern: ^[0-9]{9,12}';
$string['remoteservice'] = 'URL to the remote web service.';
$string['remoteservice_help'] = 'The user input value you want to validate is represented by the placeholder $$:
 https://example.com/api/GetInfo?fieldvalue=$$&token=1234567890';
$string['wrongpattern'] = 'Your input does not match the required pattern.';
$string['validationerror'] = 'Validation returned the following error: ';
