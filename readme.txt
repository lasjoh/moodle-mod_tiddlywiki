!!!! attention at the moment this crashes the moodle admin console with this 
errormessages: 
Exception - Call to a member function addElement() on null (after the installation of the addon)
Call to undefined method mod_tiddlywiki_mod_form::standard_coursemodule_elements() (when trying to add a the acitivity in the course-module)

This repository started as a clone of moodles Resource module. 
( https://github.com/moodle/moodle/tree/master/mod/resource which is one of the successors to original 'file' type plugin of Resource module.)
It is the intention to develop an activity-plugin with the capacity to save changes made the Tiddlywiki by a modified version of the TiddlyWiki store php.

As Moodle, it is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

As Moodle, it is is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

copyright of 2009 Petr Skoda (http://skodak.org)
license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later


FEATURES:
* The module allows to create a moodle activity
* It alows to upload and manage multiple files
* Set which file will be displayed
* Set the Wiki to display either in embedded or in a popup

TODO:
Phase 1. 
 * Adapting names in order to avoid confusion with the ressource module,
 * Add the possiblity to save changes made to the Tiddlywiki by the Tiddlywiki saver and a modified version of store.php saving as a new file and setting if the User has capabilities to edit.
 * Automatically set the most recent file to be shown.
 
 Phase 2. 
 * Adapt the store.php mechanism to keep a reasonable number of backups - per user.
 * Allow to upload images and other media-files that can be embedded in moodle.
 * Allow users to save tiddlers they modified as json-files, to be reimported in the moodle.
 
 Phase 3. 
 * Allow users to stave and view their own copies of the wiki (or a json containing the modifications)
 * Invent a mechanism for cooperation that keeps the userfiles updated to the new version.
