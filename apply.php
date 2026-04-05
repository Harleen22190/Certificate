<?php
require('../../config.php');
require_login();

$context = context_system::instance();

if (!has_capability('local/trainingrequest:apply', $context)) {
    redirect(new moodle_url('/local/trainingrequest/index.php'));
}

global $DB, $USER, $PAGE, $OUTPUT;

$PAGE->set_url('/local/trainingrequest/apply.php');
$PAGE->set_title('Apply for Training');
$PAGE->set_heading('Apply for Training');

$PAGE->requires->css('/local/trainingrequest/styles.css');

echo $OUTPUT->header();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && confirm_sesskey()) {

    $title = required_param('title', PARAM_TEXT);
    $description = optional_param('description', '', PARAM_TEXT);
    $category = optional_param('category', '', PARAM_TEXT);

    $record = new stdClass();
    $record->userid = $USER->id;
    $record->title = $title;
    $record->description = $description;
    $record->category = $category;

    $record->status = 'submitted';

    $record->timecreated = time();
    $record->timemodified = time();

    $DB->insert_record('training_requests', $record);

    redirect(
        new moodle_url('/local/trainingrequest/dashboard.php'),
        'Request submitted successfully 🚀',
        2,
        \core\output\notification::NOTIFY_SUCCESS
    );
}


echo "
<style>
html, body {
    height: 100%;
    overflow: hidden; /* remove scroll */
}

/* Moodle main container fix */
#page {
    height: 100vh;
    overflow: hidden;
}

#page-content {
    height: calc(100vh - 100px); /* adjust for header */
    overflow: hidden;
}

/* CENTER WRAPPER */
.training-wrapper {
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
}

/* CARD */
.training-card {
    width: 100%;
    max-width: 500px;
    padding: 25px;
    border-radius: 12px;
    background: #fff;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);

    max-height: 90vh;
    overflow-y: auto; /* scroll only inside card */
}
</style>
";
echo "<div class='training-wrapper'>";

echo "<div class='training-card'>";

echo "<h3>Apply for Training</h3>";
echo "<p>Submit your request professionally</p>";


echo "<div class='form-group'>";
echo "<label class='form-label'>Name</label>";
echo "<input type='text' class='form-control' value='" . fullname($USER) . "' readonly>";
echo "</div>";

echo "<div class='form-group'>";
echo "<label class='form-label'>Email</label>";
echo "<input type='text' class='form-control' value='" . s($USER->email) . "' readonly>";
echo "</div>";


echo "<form method='post' enctype='multipart/form-data'>";

echo "<input type='hidden' name='sesskey' value='" . sesskey() . "'>";
echo "<div class='form-group'>";
echo "<label class='form-label'>Course Title</label>";
echo "<input type='text' name='title' class='form-control' placeholder='Enter course name...' required>";
echo "</div>";

echo "<div class='form-group'>";
echo "<label class='form-label'>Category</label>";
echo "<select name='category' class='form-select'>";
echo "<option value=''>Select category</option>";
echo "<option value='IT'>IT</option>";
echo "<option value='Management'>Management</option>";
echo "<option value='Finance'>Finance</option>";
echo "<option value='Other'>Other</option>";
echo "</select>";
echo "</div>";

echo "<div class='form-group'>";
echo "<label class='form-label'>Description</label>";
echo "<textarea name='description' class='form-control' rows='4' placeholder='Optional details...'></textarea>";
echo "</div>";

echo "<div class='form-group'>";
echo "<label class='form-label'>Attachment (optional)</label>";
echo "<input type='file' name='attachment' class='form-control'>";
echo "</div>";

echo "<button type='submit' class='btn btn-success w-100 mt-2'>
        Submit Request
      </button>";
echo "</form>";
echo "</div>"; 
echo "</div>"; 

echo $OUTPUT->footer();
