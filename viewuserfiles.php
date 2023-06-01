<?php

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

require_once($CFG->libdir.'/adminlib.php');

require_once($CFG->libdir.'/filelib.php');

// Check if the user is logged in 

require_login();


// Set up basic page parameters

$PAGE->set_url('/mod/tiddlywiki/viewuserfiles.php');

$PAGE->set_context(context_system::instance());

$PAGE->set_title(get_string('userfiles', 'tiddlywiki'));

$PAGE->set_heading(get_string('userfiles', 'tiddlywiki'));

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('userfiles', 'tiddlywiki'));

// Get the list of all HTML files uploaded by users, sorted by user ID and upload time

$sql = "

    SELECT r.id, r.name, r.reference, r.referencefilespath, r.timemodified, u.id AS userid, u.firstname, u.lastname

    FROM {tiddlywiki} r

    JOIN {user} u ON u.id = r.author

    WHERE r.reference LIKE '%.html'

    ORDER BY u.id ASC, r.timemodified DESC

";

$tiddlywikis = $DB->get_recordset_sql($sql);

// Initialize the last uploaded file information

$prevuserid = null; // user ID of the previous file processed

$lastfilename = null; // name of the last uploaded file

$lastfiletime = null; // upload time of the last uploaded file

// Loop through each file and display its information as part of the appropriate user's row

foreach ($tiddlywikis as $tiddlywiki) {

    $userid = $tiddlywiki->userid;

    $filename = $tiddlywiki->name;

    $filepath = $tiddlywiki->referencefilespath.'/'.$tiddlywiki->reference;

    $filetime = $tiddlywiki->timemodified;

    if ($prevuserid !== $userid) {

        // Start a new row for a new user

        if ($prevuserid !== null) {

            echo '<td>'.userdate($lastfiletime).'</td>';

            echo '<td><a href="'.new moodle_url('/mod/tiddlywiki/view.php', ['id' => $lasttiddlywiki->id, 'forcedownload' => 1]).'">'.$lastfilename.'</a></td>';

            echo '<td><a href="'.new moodle_url('/mod/tiddlywiki/viewuserfiles.php', ['id' => $prevuserid]).'" class="btn btn-primary">'.get_string('viewallfiles', 'tiddlywiki').'</a></td>';

            echo '</tr>';

        }

        echo '<tr>';

        echo '<td>'.$tiddlywiki->firstname.' '.$tiddlywiki->lastname.'</td>';

        $prevuserid = $userid;

        $lastfilename = $filename;

        $lastfiletime = $filetime;

        $lasttiddlywiki = $tiddlywiki;

    } else {

        // Update the last uploaded file if it is more recent than the current one

        if ($filetime > $lastfiletime) {

            $lastfilename = $filename;

            $lastfiletime = $filetime;

            $lasttiddlywiki = $tiddlywiki;

        }

    }

}

$tiddlywikis->close();

// Display the last uploaded file information for the last user

if ($prevuserid !== null) {

    echo '<td>'.userdate($lastfiletime).'</td>';

    echo '<td><a href="'.new moodle_url('/mod/tiddlywiki/view.php', ['id' => $lasttiddlywiki->id, 'forcedownload' => 1]).'">'.$lastfilename.'</a></td>';

    echo '<td><a href="'.new moodle_url('/mod/tiddlywiki/viewuserfiles.php', ['id' => $prevuserid]).'" class="btn btn-primary">'.get_string('viewallfiles', 'tiddlywiki').'</a></td>';

    echo '</tr>';

}

echo '</table>';

echo $OUTPUT->footer();