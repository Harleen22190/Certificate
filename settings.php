<?php
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    $ADMIN->add('localplugins', new admin_externalpage(
        'local_trainingrequest',
        'Training Request',
        new moodle_url('/local/trainingrequest/index.php')
    ));
}