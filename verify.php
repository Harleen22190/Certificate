<?php
require('../../config.php');

global $DB, $PAGE, $OUTPUT;


$id = optional_param('id', 0, PARAM_INT);


$PAGE->set_url('/local/trainingrequest/verify.php', ['id' => $id]);
$PAGE->set_title('Certificate Verification');
$PAGE->set_heading('Certificate Verification');
$PAGE->requires->css('/local/trainingrequest/styles.css');

echo $OUTPUT->header();

echo "<div class='container mt-5'>";

if (!$id) {
    echo "<div class='alert alert-danger text-center'>
            Invalid certificate request.
          </div>";
    echo $OUTPUT->footer();
    exit;
}


$record = $DB->get_record('training_requests', ['id' => $id]);

if (!$record) {
    echo "<div class='alert alert-danger text-center'>
            Certificate not found.
          </div>";
    echo $OUTPUT->footer();
    exit;
}

$user = $DB->get_record('user', ['id' => $record->userid]);


if ($record->status !== 'approved') {

    echo "<div class='card p-4 shadow text-center border-danger'>";
    echo "<h3 class='text-danger'>❌ Certificate Not Valid</h3>";
    echo "<p>This request is not approved.</p>";
    echo "</div>";

} else {

    $certificateid = 'GNDEC-' . date('Y') . '-' . str_pad($record->id, 4, '0', STR_PAD_LEFT);

    echo "<div class='card p-4 shadow-lg'>";

    echo "<h3 class='text-success mb-3'>✅ Certificate Verified</h3>";

    echo "<p><strong>Student:</strong> " . fullname($user) . "</p>";
    echo "<p><strong>Training Title:</strong> " . format_string($record->title) . "</p>";

    echo "<p><strong>Status:</strong> 
            <span class='badge bg-success'>Approved</span>
          </p>";

    echo "<p><strong>Issued On:</strong> " . userdate($record->timecreated) . "</p>";

    echo "<p><strong>Certificate ID:</strong> <code>{$certificateid}</code></p>";

    echo "</div>";
}

echo "</div>";

echo $OUTPUT->footer();