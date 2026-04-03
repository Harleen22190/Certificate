<?php
require('../../config.php');
require_login();

require_capability('local/trainingrequest:manage', context_system::instance());

global $DB, $PAGE, $OUTPUT;

$PAGE->set_url('/local/trainingrequest/admin.php');
$PAGE->set_title('Admin Panel');
$PAGE->set_heading('Admin Panel');

echo $OUTPUT->header();

if (isset($_POST['assign'])) {
    $id = $_POST['requestid'];
    $facultyid = $_POST['facultyid'];

    $record = $DB->get_record('training_requests', ['id'=>$id]);
    $record->facultyid = $facultyid;
    $DB->update_record('training_requests', $record);

    echo "<p style='color:green;'>Faculty Assigned!</p>";
}

$records = $DB->get_records('training_requests');
$users = $DB->get_records('user');

echo "<h3>Assign Faculty</h3>";

foreach ($records as $r) {

    echo "<div style='border:1px solid #ccc; padding:15px; margin:10px;'>";

    echo "<b>Title:</b> {$r->title}<br>";
    echo "<b>Status:</b> {$r->status}<br><br>";

    echo "<form method='post'>";
    echo "<input type='hidden' name='requestid' value='{$r->id}'>";

    echo "<select name='facultyid'>";
    foreach ($users as $u) {
        echo "<option value='{$u->id}'>{$u->firstname} {$u->lastname}</option>";
    }
    echo "</select>";

    echo " <button type='submit' name='assign'>Assign</button>";
    echo "</form>";

    echo "</div>";
}

echo $OUTPUT->footer();
