<?php
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/mod/tiddlywiki/lib.php');

// Define the retention period for the latest files (in minutes/hours/days/weeks/months)
$retention = array('min' => 1, 'hr' => 1, 'day' => 7, 'week' => 4, 'month' => 12);

// Get the tiddlywiki instance ID and activity ID
$tiddlywikiid = required_param('id', PARAM_INT);
$cmid = required_param('cmid', PARAM_INT);

// Get the course module object
$cm = get_coursemodule_from_id('tiddlywiki', $cmid, 0, false, MUST_EXIST);

// Check if the current user has the ability to update the tiddlywiki instance
if (!has_capability('mod/tiddlywiki:update', context_module::instance($cmid))) {
    print_error('accessdenied', 'error');
}

// Check if a file was uploaded
if (!empty($_FILES['file'])) {
    // Check if the file is a valid HTML file
    if ($_FILES['file']['type'] == 'text/html') {

        // Get the current timestamp
        $timestamp = time();

        // Get the author's ID
        $authorid = $USER->id;

        // Define the destination directory for the uploaded file
        $dirpath = $CFG->dataroot.'/filedir/'.$tiddlywikiid;
        if (!file_exists($dirpath)) {
            mkdir($dirpath, 0777, true);
        }

        // Define the filename with a datestring yy0mm0dd-0hh0mm
        $filename = date('y') . sprintf('%02d', date('m')) . sprintf('%02d', date('d')) . '-' . sprintf('%02d', date('H')) . sprintf('%02d', date('i')) . '.html';

        // Define the filename for the hourly retention
        $hourlyfile = date('y') . sprintf('%02d', date('m')) . sprintf('%02d', date('d')) . '-' . sprintf('%02d', date('H')) . '.html';

        // Define the filename for the daily retention
        $dailyfile = date('y') . sprintf('%02d', date('m')) . sprintf('%02d', date('d')) . '.html';

        // Define the filename for the weekly retention
        $weeklyfile = date('y') . '-W' . date('W') . '.html';

        // Define the filename for the monthly retention
        $monthlyfile = date('y') . sprintf('%02d', date('m')) . '.html';

        // Define the target paths for the various retention periods
        $target_paths = array(
            'min' => $dirpath.'/'.$filename,
            'hr' => $dirpath.'/archive/'.date('y-m-d').'/'.$hourlyfile,
            'day' => $dirpath.'/archive/'.date('y-m-d').'/'.$dailyfile,
            'week' => $dirpath.'/archive/'.$weeklyfile,
            'month' => $dirpath.'/archive/'.$monthlyfile
        );

        // Clean up any previous copies of the uploaded file
        foreach ($target_paths as $path) {
            if (is_readable($path)) {
                unlink($path);
            }
        }

        // Move the uploaded file to the specified target path
        move_uploaded_file($_FILES['file']['tmp_name'], $target_paths['min']);

        // Set the correct file permissions
        chmod($target_paths['min'], 0666);

        // Save the tiddlywiki instance with the updated file information
        $tiddlywiki = $DB->get_record('tiddlywiki', array('id' => $tiddlywikiid), '*', MUST_EXIST);
        $tiddlywiki->name = $filename;
        $tiddlywiki->reference = $filename;
        $tiddlywiki->referencefilespath = '/'.$tiddlywikiid;
        $tiddlywiki->timemodified = time();
        $DB->update_record('tiddlywiki', $tiddlywiki);

        // Redirect back to the Resource activity
        redirect("$CFG->wwwroot/mod/tiddlywiki/view.php?id=$cmid");
    }
}
?>
