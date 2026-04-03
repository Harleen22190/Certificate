<?php
require('../../config.php');
require_login();

require_capability('local/trainingrequest:manage', context_system::instance());

global $DB, $PAGE, $OUTPUT;

$PAGE->set_url('/local/trainingrequest/admin.php');
$PAGE->set_title('Admin Panel');
$PAGE->set_heading('Admin Panel');

echo $OUTPUT->header();

$records = $DB->get_records('training_requests');

foreach ($records as $r) {
    echo "<div>";
    echo "User: {$r->userid}<br>";
    echo "Title: {$r->title}<br>";
    echo "Status: {$r->status}<br>";
    echo "</div><hr>";
}

echo $OUTPUT->footer();