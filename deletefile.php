<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/filelib.php');

// Check if the user is logged in
require_login();

// Get the tiddlywiki instance ID and session key
$tiddlywikiid = required_param('id', PARAM_INT);
$sesskey = required_param('sesskey', PARAM_ALPHANUM);

// Check if the logged-in user has the appropriate capability to delete the file
if (!has_capability('mod/tiddlywiki:delete', context_module::instance($tiddlywikiid))) {
    print_error('accessdenied', 'error');
}

// Get the tiddlywiki instance object
$tiddlywiki = $DB->get_record('tiddlywiki', ['id' => $tiddlywikiid], '*', MUST_EXIST);

// Check if the file exists and is readable
$filename = $tiddlywiki->reference;
$filepath = $tiddlywiki->referencefilespath.'/'.$filename;
if (!file_exists($filepath) || !is_readable($filepath)) {
    print_error('filenotfound', 'tiddlywiki');
}

// Delete the file
unlink($filepath);

// Update the reference information in the tiddlywiki instance record
$tiddlywiki->name = '';
$tiddlywiki->reference = '';
$tiddlywiki->referencefilespath = '';
$tiddlywiki->timemodified = time();
$DB->update_record('tiddlywiki', $tiddlywiki);

// Redirect back to the My Files page
redirect($CFG->wwwroot.'/mod/tiddlywiki/viewmyfiles.php', get_string('filedeleted','tiddlywiki'));
