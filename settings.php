<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Resource module admin settings and defaults
 *
 * @package    mod_tiddlywiki
 * @copyright  2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    require_once("$CFG->dirroot/mod/tiddlywiki/twlib.php");

    $displayoptions = tiddlywikilib_get_displayoptions(array(RESOURCELIB_DISPLAY_AUTO,
                                                           RESOURCELIB_DISPLAY_EMBED,
                                                           RESOURCELIB_DISPLAY_FRAME,
                                                           RESOURCELIB_DISPLAY_DOWNLOAD,
                                                           RESOURCELIB_DISPLAY_OPEN,
                                                           RESOURCELIB_DISPLAY_NEW,
                                                           RESOURCELIB_DISPLAY_POPUP,
                                                          ));
    $defaultdisplayoptions = array(RESOURCELIB_DISPLAY_AUTO,
                                   RESOURCELIB_DISPLAY_EMBED,
                                   RESOURCELIB_DISPLAY_DOWNLOAD,
                                   RESOURCELIB_DISPLAY_OPEN,
                                   RESOURCELIB_DISPLAY_POPUP,
                                  );

    //--- general settings -----------------------------------------------------------------------------------
    $settings->add(new admin_setting_configtext('tiddlywiki/framesize',
        get_string('framesize', 'tiddlywiki'), get_string('configframesize', 'tiddlywiki'), 130, PARAM_INT));
    $settings->add(new admin_setting_configmultiselect('tiddlywiki/displayoptions',
        get_string('displayoptions', 'tiddlywiki'), get_string('configdisplayoptions', 'tiddlywiki'),
        $defaultdisplayoptions, $displayoptions));

    //--- modedit defaults -----------------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('tiddlywikimodeditdefaults', get_string('modeditdefaults', 'admin'), get_string('condifmodeditdefaults', 'admin')));

    $settings->add(new admin_setting_configcheckbox('tiddlywiki/printintro',
        get_string('printintro', 'tiddlywiki'), get_string('printintroexplain', 'tiddlywiki'), 1));
    $settings->add(new admin_setting_configselect('tiddlywiki/display',
        get_string('displayselect', 'tiddlywiki'), get_string('displayselectexplain', 'tiddlywiki'), RESOURCELIB_DISPLAY_AUTO,
        $displayoptions));
    $settings->add(new admin_setting_configcheckbox('tiddlywiki/showsize',
        get_string('showsize', 'tiddlywiki'), get_string('showsize_desc', 'tiddlywiki'), 0));
    $settings->add(new admin_setting_configcheckbox('tiddlywiki/showtype',
        get_string('showtype', 'tiddlywiki'), get_string('showtype_desc', 'tiddlywiki'), 0));
    $settings->add(new admin_setting_configcheckbox('tiddlywiki/showdate',
        get_string('showdate', 'tiddlywiki'), get_string('showdate_desc', 'tiddlywiki'), 0));
    $settings->add(new admin_setting_configtext('tiddlywiki/popupwidth',
        get_string('popupwidth', 'tiddlywiki'), get_string('popupwidthexplain', 'tiddlywiki'), 620, PARAM_INT, 7));
    $settings->add(new admin_setting_configtext('tiddlywiki/popupheight',
        get_string('popupheight', 'tiddlywiki'), get_string('popupheightexplain', 'tiddlywiki'), 450, PARAM_INT, 7));
    $options = array('0' => get_string('none'), '1' => get_string('allfiles'), '2' => get_string('htmlfilesonly'));
    $settings->add(new admin_setting_configselect('tiddlywiki/filterfiles',
        get_string('filterfiles', 'tiddlywiki'), get_string('filterfilesexplain', 'tiddlywiki'), 0, $options));
}
