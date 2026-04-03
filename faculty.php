<?php
require('../../config.php');
require_login();

global $DB, $USER, $PAGE, $OUTPUT;

$PAGE->set_url('/local/trainingrequest/faculty.php');
$PAGE->set_title('Faculty Panel');
$PAGE->set_heading('Faculty Panel');

echo $OUTPUT->header();

echo "<h2>Assigned Training Requests</h2>";

// Get requests assigned to this faculty
$records = $DB->get_records('training_requests', ['facultyid' => $USER->id]);

if (empty($records)) {
    echo "<p>No requests assigned to you.</p>";
} else {
    foreach ($records as $r) {
        echo "<div style='border:1px solid #ccc; padding:10px; margin:10px;'>";
        echo "<b>Title:</b> {$r->title}<br>";
        echo "<b>Description:</b> " . ($r->description ? $r->description : 'N/A') . "<br>";
        echo "<b>Status:</b> {$r->status}<br>";
        echo "<b>Requested by:</b> " . $DB->get_field('user', 'username', ['id' => $r->userid]) . "<br>";
        
        // Form to update status
        echo "<form method='post' style='margin-top:10px;'>";
        echo "<input type='hidden' name='requestid' value='{$r->id}'>";
        echo "<label for='status_{$r->id}'>Update Status:</label> ";
        echo "<select name='status' id='status_{$r->id}'>";
        echo "<option value='pending'" . ($r->status == 'pending' ? ' selected' : '') . ">Pending</option>";
        echo "<option value='approved'" . ($r->status == 'approved' ? ' selected' : '') . ">Approved</option>";
        echo "<option value='rejected'" . ($r->status == 'rejected' ? ' selected' : '') . ">Rejected</option>";
        echo "</select> ";
        echo "<button type='submit'>Update</button>";
        echo "</form>";
        
        echo "</div>";
    }
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['requestid']) && isset($_POST['status'])) {
    $requestid = $_POST['requestid'];
    $newstatus = $_POST['status'];
    
    // Verify the request is assigned to this faculty
    $request = $DB->get_record('training_requests', ['id' => $requestid, 'facultyid' => $USER->id]);
    if ($request) {
        $DB->update_record('training_requests', (object)['id' => $requestid, 'status' => $newstatus]);
        echo "<p style='color:green;'>Status updated successfully!</p>";
        // Redirect to refresh the page
        redirect($PAGE->url);
    } else {
        echo "<p style='color:red;'>Error: Request not found or not assigned to you.</p>";
    }
}

echo $OUTPUT->footer();
