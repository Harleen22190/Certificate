<?php
require('../../config.php');
require_login();

$PAGE->set_url('/local/trainingrequest/index.php');
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Training Request');
$PAGE->set_heading('Training Request');

echo $OUTPUT->header();

echo "<h2>Training Request System</h2>";

echo "<a href='apply.php'>Apply</a><br>";
echo "<a href='dashboard.php'>My Dashboard</a><br>";
echo "<a href='admin.php'>Admin Panel</a><br>";
echo "<a href='faculty.php'>Faculty Panel</a><br>";

echo $OUTPUT->footer();