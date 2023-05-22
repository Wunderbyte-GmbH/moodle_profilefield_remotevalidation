 <?php
// …

namespace profile_field_remotevalidation\privacy;

class provider implements
    // This plugin does not store any personal user data.
    \core_privacy\local\metadata\null_provider {

    /**
     *
     * @return  string
     */
    public static function get_reason() : string {
        return 'privacy:metadata';
    }
}
