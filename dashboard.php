<?php
require('../../config.php');
require_login();

global $DB, $USER, $PAGE, $OUTPUT;

$PAGE->set_url('/local/trainingrequest/dashboard.php');
$PAGE->set_title('Dashboard');
$PAGE->set_heading('My Requests');

echo $OUTPUT->header();

$records = $DB->get_records('training_requests', ['userid' => $USER->id]);

foreach ($records as $r) {
    echo "<div style='border:1px solid #ccc; padding:10px; margin:10px;'>";
    echo "<b>Title:</b> {$r->title}<br>";
    echo "<b>Status:</b> {$r->status}<br>";
    echo "</div>";
}

echo $OUTPUT->footer();