<?php
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    $ADMIN->add('localplugins', new admin_externalpage(
        'local_trainingrequest',
        get_string('pluginname', 'local_trainingrequest'),
        new moodle_url('/local/trainingrequest/index.php')
    ));
}
