<?php
require('../../config.php');
require_login();

$context = context_system::instance();

if (!has_capability('local/trainingrequest:apply', $context)) {
    redirect(new moodle_url('/'));
}

global $DB, $USER, $PAGE, $OUTPUT;

$PAGE->set_url('/local/trainingrequest/dashboard.php');
$PAGE->set_title('My Dashboard');
$PAGE->set_heading('My Training Requests');

$PAGE->requires->css('/local/trainingrequest/styles.css');

$statusfilter = optional_param('status', 'all', PARAM_TEXT);

echo $OUTPUT->header();

$allrequests = $DB->get_records('training_requests', ['userid' => $USER->id], 'timecreated DESC');

/* FILTER */
$requests = [];
foreach ($allrequests as $r) {
    if ($statusfilter === 'all' || $r->status === $statusfilter) {
        $requests[] = $r;
    }
}

$total = count($allrequests);
$approved = 0;
$pending = 0;
$rejected = 0;

foreach ($allrequests as $r) {
    if ($r->status == 'approved') $approved++;
    if ($r->status == 'pending') $pending++;
    if ($r->status == 'rejected') $rejected++;
}

echo "<div class='container mt-4'>";
echo "<div class='d-flex justify-content-between align-items-center mb-4'>";
echo "<h2>Welcome, " . fullname($USER) . "</h2>";
echo "<a href='apply.php' class='btn btn-success'>+ Apply for Training</a>";
echo "</div>";

echo "<div class='row text-center mb-4'>";

echo "<div class='col-md-3'>
        <div class='card p-3 shadow-sm'>
            <h6>Total</h6>
            <h3>{$total}</h3>
        </div>
      </div>";

echo "<div class='col-md-3'>
        <div class='card p-3 shadow-sm'>
            <h6 class='text-success'>Approved</h6>
            <h3>{$approved}</h3>
        </div>
      </div>";

echo "<div class='col-md-3'>
        <div class='card p-3 shadow-sm'>
            <h6 class='text-warning'>Pending</h6>
            <h3>{$pending}</h3>
        </div>
      </div>";

echo "<div class='col-md-3'>
        <div class='card p-3 shadow-sm'>
            <h6 class='text-danger'>Rejected</h6>
            <h3>{$rejected}</h3>
        </div>
      </div>";

echo "</div>";

echo "<div class='mb-4'>";

$tabs = ['all' => 'All', 'approved' => 'Approved', 'pending' => 'Pending', 'rejected' => 'Rejected'];

foreach ($tabs as $key => $label) {

    $active = ($statusfilter == $key) ? 'btn-dark' : 'btn-outline-dark';

    echo "<a href='?status={$key}' class='btn {$active} me-2 mb-2'>{$label}</a>";
}

echo "</div>";

if (empty($requests)) {

    echo "<div class='alert alert-info text-center mt-4'>
            No requests found for this filter.
          </div>";

} else {

    echo "<div class='row'>";

    foreach ($requests as $r) {

        $color = 'warning';
        if ($r->status == 'approved') $color = 'success';
        if ($r->status == 'rejected') $color = 'danger';

        echo "<div class='col-md-4'>";
        echo "<div class='card p-3 mb-4 shadow-sm h-100'>";

        echo "<h5>" . format_string($r->title) . "</h5>";

        echo "<span class='badge bg-{$color} mb-3'>" . ucfirst($r->status) . "</span>";

        /* ACTIONS */
        if ($r->status == 'approved') {

            echo "<div class='d-grid gap-2'>";

            echo "<a href='certificate.php?id={$r->id}' 
                    class='btn btn-primary btn-sm'>
                    Download Certificate
                  </a>";

            echo "<a href='verify.php?id={$r->id}' 
                    class='btn btn-outline-secondary btn-sm'>
                    Verify Certificate
                  </a>";

            echo "</div>";
        }

        if ($r->status == 'rejected') {
            echo "<div class='text-danger small'>Request was rejected</div>";
        }

        if ($r->status == 'pending') {
            echo "<div class='text-muted small'>Waiting for approval</div>";
        }

        echo "</div></div>";
    }

    echo "</div>";
}

echo "</div>";

echo $OUTPUT->footer();
