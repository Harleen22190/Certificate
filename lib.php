function local_trainingrequest_extend_navigation(global_navigation $nav) {
    global $PAGE;

    if (isloggedin() && !isguestuser()) {

        $url = new moodle_url('/local/trainingrequest/index.php');

        $nav->add(
            'Training Request',
            $url,
            navigation_node::TYPE_CUSTOM,
            null,
            'trainingrequest',
            new pix_icon('i/report', '')
        );
    }
}
