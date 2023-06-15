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
 * Plugin strings are defined here.
 *
 * @package     mod_tiddlywiki
 * @category    string
 * @copyright   2023 Jan Johannpeter <jan@szen.io>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


$string['modulename'] = 'TiddlyWiki';
$string['general'] = 'TiddlyWiki';
$string['tiddlywikiname'] = 'Title';
$string['pluginname'] = 'TiddlyWiki';
$string['tiddlywikisettings'] = 'Settings';
$string['tiddlywiki:savechanges'] = 'Save Changes';

$string['clicktodownload'] = 'Click {$a} link to download the Wiki.';
$string['clicktoopen2'] = 'Click {$a} link to view the Wiki.';
$string['configdisplayoptions'] = 'Select all options that should be available, existing settings are not modified. Hold CTRL key to select multiple fields.';
$string['configframesize'] = 'When a web page or an uploaded TdidlyWiki is displayed within a frame, this value is the height (in pixels) of the top frame (which contains the navigation).';
$string['configparametersettings'] = 'This sets the default value for the Parameter settings pane in the form when adding some new tiddlywikis. After the first time, this becomes an individual user preference.';
$string['configpopup'] = 'When adding a new tiddlywiki which is able to be shown in a popup window, should this option be enabled by default?';
$string['configpopupdirectories'] = 'Should popup windows show directory links by default?';
$string['configpopupheight'] = 'What height should be the default height for new popup windows?';
$string['configpopuplocation'] = 'Should popup windows show the location bar by default?';
$string['configpopupmenubar'] = 'Should popup windows show the menu bar by default?';
$string['configpopupresizable'] = 'Should popup windows be resizable by default?';
$string['configpopupscrollbars'] = 'Should popup windows be scrollable by default?';
$string['configpopupstatus'] = 'Should popup windows show the status bar by default?';
$string['configpopuptoolbar'] = 'Should popup windows show the tool bar by default?';
$string['configpopupwidth'] = 'What width should be the default width for new popup windows?';
$string['contentheader'] = 'Content';
$string['displayoptions'] = 'Available display options';
$string['displayselect'] = 'Display';
$string['displayselect_help'] = 'This setting, together the question whether the browser allows embedding, determines how the Tiddlywiki is displayed. Options may include:

* Automatic - The best display option for the file type is selected automatically
* Embed - The file is displayed within the page below the navigation bar together with the file description and any blocks
* Force download - The user is prompted to download the file
* Open - Only the file is displayed in the browser window
* In pop-up - The file is displayed in a new browser window without menus or an address bar
* In frame - The file is displayed within a frame below the navigation bar and file description
* New window - The file is displayed in a new browser window with menus and an address bar';
$string['displayselect_link'] = 'mod/file/mod';
$string['displayselectexplain'] = 'Choose display type, unfortunately not all types are suitable for all files.';
$string['dnduploadtiddlywiki'] = 'Create tiddlywiki';
$string['encryptedcode'] = 'Encrypted code';
$string['filenotfound'] = 'Wiki not found, sorry.';
$string['filterfiles'] = 'Use filters on file content';
$string['filterfilesexplain'] = 'Select type of file content filtering, please note this may cause problems for some Flash and Java applets. Please make sure that all text files are in UTF-8 encoding.';
$string['filtername'] = 'Resource names auto-linking';
$string['forcedownload'] = 'Force download';
$string['framesize'] = 'Frame height';
$string['indicator:cognitivedepth'] = 'File cognitive';
$string['indicator:cognitivedepth_help'] = 'This indicator is based on the cognitive depth reached by the student in a tiddlywiki.';
$string['indicator:cognitivedepthdef'] = 'File cognitive';
$string['indicator:cognitivedepthdef_help'] = 'The participant has reached this percentage of the cognitive engagement offered by the File tiddlywikis during this analysis interval (Levels = No view, View)';
$string['indicator:cognitivedepthdef_link'] = 'Learning_analytics_indicators#Cognitive_depth';
$string['indicator:socialbreadth'] = 'File social';
$string['indicator:socialbreadth_help'] = 'This indicator is based on the social breadth reached by the student in a File tiddlywiki.';
$string['indicator:socialbreadthdef'] = 'File social';
$string['indicator:socialbreadthdef_help'] = 'The participant has reached this percentage of the social engagement offered by the tiddlywikis during this analysis interval (Levels = No participation, Participant alone)';
$string['indicator:socialbreadthdef_link'] = 'Learning_analytics_indicators#Social_breadth';
$string['legacyfiles'] = 'Migration of old course Wiki';
$string['legacyfilesactive'] = 'Active';
$string['legacyfilesdone'] = 'Finished';
$string['modifieddate'] = 'Modified {$a}';
$string['modulename'] = 'TiddlyWiki';
$string['modulename_help'] = 'With this module, xou can integrate a  <a href="https://tiddlywiki.com/">Tiddlyiki</a>, <br>
that can serve as coursebook ans as a non-linear notebook for organising and sharing complex information. It can be contained in the form of a single HTML file that includes CSS, JavaScript, embedded files such as images, and the text content. It is designed to be easy to customize and reshape. 
It may be used as a flexible coursebook.<br>
Where possible, the Wiki will be displayed within the course interface with th option to save changes made; otherwise students will be prompted to download it.
';
$string['modulename_link'] = 'mod/tiddlywiki/view';
$string['modulenameplural'] = 'Wikis';
$string['notmigrated'] = 'This legacy tiddlywiki type ({$a}) was not yet migrated, sorry.';
$string['optionsheader'] = 'Display options';
$string['page-mod-tiddlywiki-x'] = 'Any TiddlyWiki module page';
$string['pluginadministration'] = 'TiddlyWiki module administration';
$string['pluginname'] = 'TiddlyWiki';
$string['popupheight'] = 'Pop-up height (in pixels)';
$string['popupheightexplain'] = 'Specifies default height of popup windows.';
$string['popuptiddlywiki'] = 'This tiddlywiki should appear in a popup window.';
$string['popuptiddlywikilink'] = 'If it didn\'t, click here: {$a}';
$string['popupwidth'] = 'Pop-up width (in pixels)';
$string['popupwidthexplain'] = 'Specifies default width of popup windows.';
$string['printintro'] = 'Display tiddlywiki description';
$string['printintroexplain'] = 'Display tiddlywiki description below content? Some display types may not display description even if enabled.';
$string['privacy:metadata'] = 'The tiddlywiki plugin does not store any personal metadata.';
$string['tiddlywiki:addinstance'] = 'Add a new tiddlywiki';
$string['tiddlywikicontent'] = 'Wikis and subfolders';
$string['tiddlywikidetails_sizetype'] = '{$a->size} {$a->type}';
$string['tiddlywikidetails_sizedate'] = '{$a->size} {$a->date}';
$string['tiddlywikidetails_typedate'] = '{$a->type} {$a->date}';
$string['tiddlywikidetails_sizetypedate'] = '{$a->size} {$a->type} {$a->date}';
$string['tiddlywiki:exporttiddlywiki'] = 'Export tiddlywiki';
$string['tiddlywiki:view'] = 'View tiddlywiki';
$string['search:activity'] = 'TiddlyWiki';
$string['selectmainfile'] = 'Please select the main TiddlyWiki by clicking the icon next to the Wikis name.';
$string['showdate'] = 'Show upload/modified date';
$string['showdate_desc'] = 'Display upload/modified date on course page?';
$string['showdate_help'] = 'Displays the upload/modified date beside links to the file.

If there are multiple files in this tiddlywiki, the start file upload/modified date is displayed.';
$string['showsize'] = 'Show size';
$string['showsize_help'] = 'Displays the file size, such as \'3.1 MB\', beside links to the file.

If there are multiple files in this tiddlywiki, the total size of all files is displayed.';
$string['showsize_desc'] = 'Display file size on course page?';
$string['showtype'] = 'Show type';
$string['showtype_desc'] = 'Display file type (e.g. \'Word document\') on course page?';
$string['showtype_help'] = 'Displays the type of the file, beside links to the file.

If there are multiple files in this tiddlywiki, the start file type is displayed.

If the file type is not known to the system, it will not display.';
$string['uploadeddate'] = 'Uploaded {$a}';
