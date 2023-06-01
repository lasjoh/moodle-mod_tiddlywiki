<?php

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_heading('tiddlywiki', get_string('modulename', 'tiddlywiki'), ''));

    $settings->add(new admin_setting_configcheckbox('mod_tiddlywiki/enablegroupings',
           new lang_string('groupingson', 'admin'),
           new lang_string('groupingson_desc', 'admin'),
           '1'));

    $settings->add(new admin_setting_configtext('mod_tiddlywiki/groupmode', new lang_string('groupmode', 'admin'),
                                new lang_string('groupmode_desc', 'admin'), '0'));

    $settings->add(new admin_setting_configtext('mod_tiddlywiki/groupmemberingroup', new lang_string('groupmemberingroup', 'admin'),
                                                new lang_string('groupmemberingroup_desc', 'admin'), '0'));

    $settings->add(new admin_setting_configcheckbox('mod_tiddlywiki/allowlocalfiles', new lang_string('allowlocalfiles', 'tiddlywiki'),
                                           new lang_string('allowlocalfiles_desc', 'tiddlywiki'), 0));

    $settings->add(new admin_setting_configselect('mod_tiddlywiki/maxbytes', new lang_string('maximumuploadsize', 'admin'), new lang_string('maximumuploadsize_desc', 'admin'), '104857600', get_max_upload_sizes()));

    $settings->add(new admin_setting_configcheckbox('mod_tiddlywiki/allowupdatefile',
        "Allow users to update the default file shown",
        "Enable this option to allow users with the 'mod/tiddlywiki:update' capability to update the default file shown in the Resource activity.",
        1));

    $settings->add(new admin_setting_configmultiselect('mod_tiddlywiki/uploadusers',
        "Users who can upload files with store.php",
        "Select the roles that can upload files using the store.php script in the Resource activity:",
        array('teacher' => 'Teacher', 'editingteacher' => 'Editing teacher', 'manager' => 'Manager', 'authenticateduser' => 'Authenticated user'),
        array('teacher', 'editingteacher', 'manager', 'authenticateduser')));

    $settings->add(new admin_setting_configcheckbox('mod_tiddlywiki/allowuploadmedia',
        "Allow users to upload media files for use in the Wiki activity",
        "Enable this option to allow users with upload permissions in the Wiki activity to upload media files that can be used in the wiki.",
        1));

    $settings->add(new admin_setting_configmultiselect('mod_tiddlywiki/uploadmediaroles',  // Setting name
        "Roles that can upload media files for the Wiki activity",  // Setting title
        "Select the roles that can upload media files that can be used in the Wiki activity:",  // Setting description
        array('editingteacher' => 'Editing teacher', 'manager' => 'Manager'),  // Options
        array('editingteacher', 'manager')));  // Default values
		
		// This is added to allow or forbid to view other users files.

$mform->addElement('selectyesno', 'viewuserfiles', get_string('view_user_versions', 'mod_tiddlywiki'));

$mform->setDefault('viewuserfiles', isset($tiddlywiki->viewuserfiles) ? $tiddlywiki->viewuserfiles : 0);

$mform->addHelpButton('viewuserfiles', 'view_user_versions_help', 'mod_tiddlywiki');

// Set up permissions

if (!empty($CFG->enableavailability)) {

    $options = ['none' => get_string('notavailable', 'condition'),

                'available' => get_string('available', 'condition'),

                'view' => get_string('availabletoview', 'condition')];

} else {

    $options = ['none' => get_string('no'),

                'view' => get_string('yes')];

}

$setting = new admin_setting_configselect('mod_tiddlywiki/viewuserfiles', get_string('view_user_versions', 'mod_tiddlywiki'),

            get_string('view_user_versions_help', 'mod_tiddlywiki'), 'none', $options);

$setting->set_updatedcallback('reset_all_caches');

$settings->add($setting);

}
