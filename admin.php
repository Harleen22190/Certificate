<?php
require('../../config.php');
require_login();

$context = context_system::instance();
require_capability('local/trainingrequest:manage', $context);

global $DB, $PAGE, $OUTPUT, $USER;

$PAGE->set_url('/local/trainingrequest/admin.php');
$PAGE->set_title('Admin Dashboard');
$PAGE->set_heading('Admin Dashboard');

echo $OUTPUT->header();


$search = optional_param('search', '', PARAM_TEXT);
$status = optional_param('status', '', PARAM_ALPHA);

$sort = optional_param('sort', 'timecreated', PARAM_ALPHA);
$dir = optional_param('dir', 'DESC', PARAM_ALPHA);

$page = optional_param('page', 0, PARAM_INT);
$limit = 5;
$offset = $page * $limit;

if (optional_param('assign', 0, PARAM_BOOL) && confirm_sesskey()) {

    $id = required_param('requestid', PARAM_INT);
    $facultyid = required_param('facultyid', PARAM_INT);

    if ($record = $DB->get_record('training_requests', ['id' => $id])) {

        if (!empty($record->facultyid)) {
            redirect(new moodle_url('/local/trainingrequest/admin.php'), 'Already assigned ❌');
        }

        $record->facultyid = $facultyid;

        $record->status = 'under_review';
        $record->timemodified = time();

        $DB->update_record('training_requests', $record);

        redirect(new moodle_url('/local/trainingrequest/admin.php'), 'Assigned & Sent to Faculty ✅');
    }
}

$where = [];
$params = [];

if ($search !== '') {
    $where[] = "(tr.title LIKE :s1 OR u.firstname LIKE :s2 OR u.lastname LIKE :s3)";
    $params['s1'] = "%$search%";
    $params['s2'] = "%$search%";
    $params['s3'] = "%$search%";
}

if ($status !== '') {
    $where[] = "tr.status = :status";
    $params['status'] = $status;
}

$sql = "FROM {training_requests} tr
        JOIN {user} u ON u.id = tr.userid
        LEFT JOIN {user} f ON f.id = tr.facultyid";

if (!empty($where)) {
    $sql .= " WHERE " . implode(' AND ', $where);
}

$total = $DB->count_records_sql("SELECT COUNT(1) $sql", $params);


$allowed_sort = ['title', 'status', 'timecreated'];
if (!in_array($sort, $allowed_sort)) {
    $sort = 'timecreated';
}

$dir = ($dir === 'ASC') ? 'ASC' : 'DESC';

$records = $DB->get_records_sql("
    SELECT tr.*, 
           u.firstname, u.lastname,
           f.firstname AS fname, f.lastname AS flname
    $sql
    ORDER BY $sort $dir
", $params, $offset, $limit);


$faculties = $DB->get_records_sql("
    SELECT DISTINCT u.id, u.firstname, u.lastname
    FROM {user} u
    JOIN {role_assignments} ra ON ra.userid = u.id
    JOIN {role} r ON r.id = ra.roleid
    WHERE r.shortname IN ('editingteacher','teacher')
    AND u.deleted = 0
");

echo "<div class='container mt-4'>";
echo "<h2>Welcome, " . fullname($USER) . "</h2>";


echo "<form method='get' class='d-flex gap-2 mb-4'>";

echo "<input type='text' name='search' value='".s($search)."' class='form-control' placeholder='Search...' style='max-width:300px;'>";

echo "<select name='status' class='form-select' style='max-width:200px;'>
<option value=''>All</option>
<option value='submitted' ".($status=='submitted'?'selected':'').">Submitted</option>
<option value='under_review' ".($status=='under_review'?'selected':'').">Under Review</option>
<option value='approved' ".($status=='approved'?'selected':'').">Approved</option>
<option value='completed' ".($status=='completed'?'selected':'').">Completed</option>
<option value='rejected' ".($status=='rejected'?'selected':'').">Rejected</option>
</select>";

echo "<button class='btn btn-primary'>Search</button>";

echo "</form>";

function sort_link($label, $field, $sort, $dir, $search, $status) {

    $newdir = ($sort === $field && $dir === 'ASC') ? 'DESC' : 'ASC';

    $url = new moodle_url('/local/trainingrequest/admin.php', [
        'sort' => $field,
        'dir' => $newdir,
        'search' => $search,
        'status' => $status
    ]);

    return "<a href='{$url}' style='color:white;'>$label</a>";
}

echo "<table class='table table-bordered table-hover'>";
echo "<thead class='table-dark'><tr>";

echo "<th>".sort_link('Title','title',$sort,$dir,$search,$status)."</th>";
echo "<th>Student</th>";
echo "<th>Faculty</th>";
echo "<th>".sort_link('Status','status',$sort,$dir,$search,$status)."</th>";
echo "<th>".sort_link('Date','timecreated',$sort,$dir,$search,$status)."</th>";
echo "<th>Action</th>";

echo "</tr></thead><tbody>";

foreach ($records as $r) {


    switch ($r->status) {
        case 'submitted': $badge = 'secondary'; break;
        case 'under_review': $badge = 'info'; break;
        case 'approved': $badge = 'success'; break;
        case 'completed': $badge = 'primary'; break;
        case 'rejected': $badge = 'danger'; break;
        default: $badge = 'warning';
    }

    echo "<tr>";

    echo "<td>".format_string($r->title)."</td>";
    echo "<td>{$r->firstname} {$r->lastname}</td>";

    echo "<td>";
    if ($r->facultyid) {
        echo "{$r->fname} {$r->flname}";
    } else {
        echo "<span class='text-muted'>Not Assigned</span>";
    }
    echo "</td>";

    echo "<td><span class='badge bg-$badge'>".ucfirst(str_replace('_',' ', $r->status))."</span></td>";

    echo "<td>".date('d M Y', $r->timecreated)."</td>";

    echo "<td>";

    if (!$r->facultyid) {

        echo "<form method='post' class='d-flex gap-2'>";
        echo "<input type='hidden' name='sesskey' value='".sesskey()."'>";
        echo "<input type='hidden' name='requestid' value='{$r->id}'>";

        echo "<select name='facultyid' class='form-select form-select-sm'>";
        echo "<option value=''>Select Faculty</option>";
        foreach ($faculties as $f) {
            echo "<option value='{$f->id}'>".fullname($f)." (Faculty)</option>";
        }
        echo "</select>";

        echo "<button name='assign' value='1' class='btn btn-sm btn-primary'>Assign</button>";

        echo "</form>";

    } else {
        echo "<button class='btn btn-sm btn-secondary' disabled>Assigned</button>";
    }

    echo "</td></tr>";
}

echo "</tbody></table>";
$totalpages = ceil($total / $limit);

echo "<div>";
for ($i = 0; $i < $totalpages; $i++) {

    $url = new moodle_url('/local/trainingrequest/admin.php', [
        'page' => $i,
        'search' => $search,
        'status' => $status,
        'sort' => $sort,
        'dir' => $dir
    ]);

    echo "<a class='btn btn-sm btn-outline-primary me-1' href='{$url}'>".($i+1)."</a>";
}
echo "</div>";

echo "</div>";
echo $OUTPUT->footer();
