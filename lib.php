<?php
defined('MOODLE_INTERNAL') || die();

function local_trainingrequest_extend_navigation(global_navigation $nav) {

    if (!isloggedin() || isguestuser()) {
        return;
    }

    $context = context_system::instance();

    if (!has_capability('local/trainingrequest:apply', $context) &&
        !has_capability('local/trainingrequest:manage', $context)) {
        return;
    }

    $url = new moodle_url('/local/trainingrequest/index.php');

    $nav->add(
        get_string('pluginname', 'local_trainingrequest'),
        $url,
        navigation_node::TYPE_CUSTOM,
        null,
        'trainingrequest',
        new pix_icon('i/report', '')
    );
}
function local_trainingrequest_extend_navigation_course($navigation, $course, $context) {

    if (!isloggedin() || isguestuser()) {
        return;
    }

    if (has_capability('local/trainingrequest:apply', $context) ||
        has_capability('local/trainingrequest:manage', $context)) {

        $url = new moodle_url('/local/trainingrequest/index.php', [
            'courseid' => $course->id
        ]);

        $navigation->add(
            'Training Request',
            $url,
            navigation_node::TYPE_CUSTOM,
            null,
            'trainingrequest',
            new pix_icon('i/report', '')
        );
    }
}
function local_trainingrequest_assign_faculty() {
    global $DB;

    $faculty = $DB->get_record_sql("
        SELECT u.id
        FROM {user} u
        JOIN {role_assignments} ra ON ra.userid = u.id
        JOIN {role} r ON r.id = ra.roleid
        WHERE r.shortname IN ('editingteacher','teacher')
        AND u.deleted = 0
        ORDER BY u.id ASC
        LIMIT 1
    ");

    return $faculty ? $faculty->id : null;
}
function local_trainingrequest_notify($userid, $message) {

    $eventdata = new \core\message\message();

    $eventdata->component = 'local_trainingrequest';
    $eventdata->name = 'notification';

    $eventdata->userfrom = \core_user::get_noreply_user();
    $eventdata->userto = $userid;

    $eventdata->subject = 'Training Request Update';

    $eventdata->fullmessage = $message;
    $eventdata->fullmessageformat = FORMAT_PLAIN;

    $eventdata->fullmessagehtml = '<p>' . format_string($message) . '</p>';
    $eventdata->smallmessage = $message;

    $eventdata->notification = 1;

    message_send($eventdata);
}
function local_trainingrequest_extend_navigation_frontpage(navigation_node $frontpage) {
}
