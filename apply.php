<?php
require('../../config.php');
require_login();

global $DB, $USER;$PAGE, $OUTPUT;

$PAGE->set_url('/local/trainingrequest/dashboard.php');
$page->set_title('My Training Requests');
$page->set_heading('My Training Requests');

echo $output->header();

if ($_POST){
    $record = new stdClass();
    $record->userid = $USER->id;
    $record->title = $_POST['title'];
    $record->status = 'Pending';
    $record->timecreated = time();
    
    $DB->insert_record('training_requests', $record);

    echo "<p style='color:green;'>Request Submitted!"</p>";
}
?>

<form method='post'>
<h2>Apply for Training</h2>
<label for='title'>Course Title:</label><br>
<input type='text' id='title' name='title' required><br><br>    
<button type='submit'>Submit Request</button>
</form>
echo $output->footer();
}