<?php
require('../../config.php');
require_login();

$context = context_system::instance();
global $PAGE, $OUTPUT;

$PAGE->set_url('/local/trainingrequest/index.php');
$PAGE->set_title('Training Request System');
$PAGE->set_heading('Training Request System');

echo $OUTPUT->header();

echo "<div class='container mt-4 text-center'>";

echo "<h2>Training Request System</h2>";

echo "<div class='mt-4'>";

echo "<a href='apply.php' class='btn btn-success me-2'>Apply</a>";
echo "<a href='dashboard.php' class='btn btn-primary me-2'>My Dashboard</a>";

if (has_capability('local/trainingrequest:manage', context_system::instance())) {
    echo "<a href='admin.php' class='btn btn-dark me-2'>Admin Panel</a>";
    echo "<a href='faculty.php' class='btn btn-warning'>Faculty Panel</a>";
}

echo "</div>";
echo "</div>";

echo $OUTPUT->footer();
