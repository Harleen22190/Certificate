<?php
require('../../config.php');
require_login();

$context = context_system::instance();

require_capability('local/trainingrequest:manage', $context);

global $DB, $USER, $PAGE, $OUTPUT;

$PAGE->set_url('/local/trainingrequest/faculty.php');
$PAGE->set_title('Faculty Panel');
$PAGE->set_heading('Faculty Panel');

$PAGE->requires->css('/local/trainingrequest/styles.css');
if (($approveid = optional_param('approve', 0, PARAM_INT)) && confirm_sesskey()) {

    if ($record = $DB->get_record('training_requests', [
        'id' => $approveid,
        'facultyid' => $USER->id
    ])) {

        if ($record->status === 'under_review') {

            $remarks = optional_param('remarks', '', PARAM_TEXT);

            $record->status = 'approved';
            $record->remarks = $remarks;
            $record->approvedby = $USER->id;
            $record->timeapproved = time();
            $record->timemodified = time();

            $DB->update_record('training_requests', $record);

            local_trainingrequest_notify($record->userid, '✅ Your request has been approved.');
        }
    }

    redirect(new moodle_url('/local/trainingrequest/faculty.php'));
}

if (($rejectid = optional_param('reject', 0, PARAM_INT)) && confirm_sesskey()) {

    if ($record = $DB->get_record('training_requests', [
        'id' => $rejectid,
        'facultyid' => $USER->id
    ])) {

        if ($record->status === 'under_review') {

            $remarks = optional_param('remarks', '', PARAM_TEXT);

            $record->status = 'rejected';
            $record->remarks = $remarks;
            $record->timemodified = time();

            $DB->update_record('training_requests', $record);

            local_trainingrequest_notify($record->userid, '❌ Your request has been rejected.');
        }
    }

    redirect(new moodle_url('/local/trainingrequest/faculty.php'));
}

$requests = $DB->get_records('training_requests', [
    'facultyid' => $USER->id
], 'timecreated DESC');

$pending = $DB->count_records('training_requests', [
    'facultyid' => $USER->id,
    'status' => 'under_review'
]);

$approved = $DB->count_records('training_requests', [
    'facultyid' => $USER->id,
    'status' => 'approved'
]);

$rejected = $DB->count_records('training_requests', [
    'facultyid' => $USER->id,
    'status' => 'rejected'
]);

echo $OUTPUT->header();

echo "<div class='container mt-4'>";
echo "<h2 class='mb-4'>Faculty Dashboard</h2>";
echo "<div class='row mb-4'>";

echo "<div class='col-md-4'>
        <div class='card p-3 text-center shadow-sm'>
            <h5>Under Review</h5>
            <h3 class='text-warning'>{$pending}</h3>
        </div>
      </div>";

echo "<div class='col-md-4'>
        <div class='card p-3 text-center shadow-sm'>
            <h5>Approved</h5>
            <h3 class='text-success'>{$approved}</h3>
        </div>
      </div>";

echo "<div class='col-md-4'>
        <div class='card p-3 text-center shadow-sm'>
            <h5>Rejected</h5>
            <h3 class='text-danger'>{$rejected}</h3>
        </div>
      </div>";

echo "</div>";

if (empty($requests)) {

    echo "<div class='alert alert-info text-center'>
            No training requests assigned to you.
          </div>";

} else {

    echo "<div class='table-responsive'>";
    echo "<table class='table table-bordered table-hover align-middle'>";

    echo "<thead class='table-dark'>
            <tr>
                <th>Student</th>
                <th>Course</th>
                <th>Status</th>
                <th>Remarks</th>
                <th>Action</th>
            </tr>
          </thead><tbody>";

    foreach ($requests as $r) {
        $student = $DB->get_record('user', ['id' => $r->userid]);
        switch ($r->status) {
            case 'under_review':
                $badge = 'warning';
                break;
            case 'approved':
                $badge = 'success';
                break;
            case 'rejected':
                $badge = 'danger';
                break;
            default:
                $badge = 'secondary';
        }

        echo "<tr>";

        echo "<td>" . fullname($student) . "</td>";
        echo "<td>" . format_string($r->title) . "</td>";

        echo "<td>
                <span class='badge bg-{$badge}'>
                    " . ucfirst(str_replace('_',' ', $r->status)) . "
                </span>
              </td>";

        echo "<td>" . (!empty($r->remarks) ? s($r->remarks) : '-') . "</td>";

        echo "<td>";

        if ($r->status === 'under_review') {

            echo "<form method='post' class='d-flex gap-2'>";
            echo "<input type='hidden' name='sesskey' value='" . sesskey() . "'>";

            echo "<input type='text' name='remarks' placeholder='Remarks...' class='form-control form-control-sm'>";

            echo "<button name='approve' value='{$r->id}' 
                        class='btn btn-success btn-sm'>
                        Approve
                  </button>";

            echo "<button name='reject' value='{$r->id}' 
                        class='btn btn-danger btn-sm'>
                        Reject
                  </button>";

            echo "</form>";

        } else {

            echo "<span class='text-muted'>Action completed</span>";
        }

        echo "</td>";
        echo "</tr>";
    }

    echo "</tbody></table>";
    echo "</div>";
}

echo "</div>";

echo $OUTPUT->footer();
