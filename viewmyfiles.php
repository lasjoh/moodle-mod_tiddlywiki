<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/filelib.php');

// Set up basic page parameters
$PAGE->set_url('/mod/tiddlywiki/viewmyfiles.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('myfiles', 'tiddlywiki'));
$PAGE->set_heading(get_string('myfiles', 'tiddlywiki'));

// Check if the user is logged in
require_login();

// Get the current user's information
$userid = $USER->id;

// Get all the tiddlywiki instances uploaded by the user
$tiddlywikis = $DB->get_records_sql("
    SELECT r.*, uu.id AS userid, uu.firstname, uu.lastname
    FROM {tiddlywiki} r
    JOIN {user} uu ON uu.id = r.author
    WHERE r.author = ? AND r.reference LIKE '%.html'
    ORDER BY r.timemodified DESC
", [$userid]);

// Check if any files were found
if (empty($tiddlywikis)) {
    echo '<p>'.get_string('nofiles', 'tiddlywiki').'</p>';
} else {
    echo '<table>';
    echo '<tr><th>'.get_string('file', 'tiddlywiki').'</th><th>'.get_string('author').'</th><th>'.get_string('timeupdated').'</th><th>'.get_string('action').'</th></tr>';

    // Loop through each tiddlywiki instance and display its information
    foreach ($tiddlywikis as $tiddlywiki) {
        $filename = $tiddlywiki->reference;
        $filepath = $tiddlywiki->referencefilespath.'/'.$filename;
        $timemodified = userdate($tiddlywiki->timemodified, get_string('strftimedatetime'));
        $deleteurl = new moodle_url('/mod/tiddlywiki/delete.php', ['id' => $tiddlywiki->id, 'sesskey' => sesskey()]);
        $viewurl = new moodle_url('/mod/tiddlywiki/view.php', ['id' => $tiddlywiki->id, 'forcedownload' => 1]);

        echo '<tr>';
        echo '<td><a href="'.$viewurl.'">'.$filename.'</a></td>';
        echo '<td>'.$tiddlywiki->firstname.' '.$tiddlywiki->lastname.'</td>';
        echo '<td>'.$timemodified.'</td>';
        echo '<td><a href="'.$deleteurl.'">'.get_string('delete').'</a></td>';
        echo '</tr>';
    }

    echo '</table>';
}

echo $OUTPUT->footer();
<?php

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

require_once($CFG->libdir.'/adminlib.php');

require_once($CFG->libdir.'/filelib.php');

// Set up basic page parameters

$PAGE->set_url('/mod/tiddlywiki/viewmyfiles.php');

$PAGE->set_context(context_system::instance());

$PAGE->set_title(get_string('myfiles', 'tiddlywiki'));

$PAGE->set_heading(get_string('myfiles', 'tiddlywiki'));

// Check if the user is logged in

require_login();

// Get the current user's information

$userid = $USER->id;

// Check if a file was deleted previously and display a success message

if (array_key_exists('filedeleted', $_GET)) {

    echo '<div class="alert alert-success">'.get_string('filedeleted', 'tiddlywiki').'</div>';

}

// Get all the tiddlywiki instances uploaded by the user

$tiddlywikis = $DB->get_records_sql("

    SELECT r.*, uu.id AS userid, uu.firstname, uu.lastname

    FROM {tiddlywiki} r

    JOIN {user} uu ON uu.id = r.author

    WHERE r.author = ? AND r.reference LIKE '%.html'

    ORDER BY r.timemodified DESC

", [$userid]);

// Check if any files were found

if (empty($tiddlywikis)) {

    echo '<p>'.get_string('nofiles', 'tiddlywiki').'</p>';

} else {

    echo '<table>';

    echo '<tr><th>'.get_string('file', 'tiddlywiki').'</th><th>'.get_string('author').'</th><th>'.get_string('timeupdated').'</th><th>'.get_string('action').'</th></tr>';

    // Loop through each tiddlywiki instance and display its information

    foreach ($tiddlywikis as $tiddlywiki) {

        $filename = $tiddlywiki->reference;

        $filepath = $tiddlywiki->referencefilespath.'/'.$filename;

        $timemodified = userdate($tiddlywiki->timemodified, get_string('strftimedatetime'));

        $deleteurl = new moodle_url('/mod/tiddlywiki/deletefile.php', ['id' => $tiddlywiki->id, 'sesskey' => sesskey()]);

        $viewurl = new moodle_url('/mod/tiddlywiki/view.php', ['id' => $tiddlywiki->id, 'forcedownload' => 1]);

        echo '<tr>';

        echo '<td><a href="'.$viewurl.'">'.$filename.'</a></td>';

        echo '<td>'.$tiddlywiki->firstname.' '.$tiddlywiki->lastname.'</td>';

        echo '<td>'.$timemodified.'</td>';

        echo '<td><a href="'.$deleteurl.'" class="btn btn-danger">'.get_string('delete').'</a></td>';

        echo '</tr>';

    }

    echo '</table>';

}

echo $OUTPUT->footer();