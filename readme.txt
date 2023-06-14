
⠀⠀⠀⠀ ⠀⢀⣤⣤⣴⣶⣶⣶⣿⣿⠟⠋⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀
⠀⠀⠀⢀⣤⣾⣿⣿⣿⣿⣿⣿⣿⣿⡁⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀
⢀⣤⣾⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣧⠀⠀⠀⢀⣠⣤⣶⣶⣤⣄⡀⠀⠀⠀
⠀⢸⡇⠀⠀⣿⣿⣿⣿⣿⣿⣿⣿⠟⢀⣴⣷⣴⣿⣿⣿⣿⣿⣿⣿⣿⣷⡄⠀
⠀⢸⡇⠀⠀⠙⠛⠿⠿⢿⣿⠟⠁⠐⢿⣿⣿⣿⣿⡿⠛⠉⠉⠙⢿⣿⣿⣿⡆
 ⢸⡇ ⠀⠀⣿⣿⣿⣿⠀⠀⠀⠀⠀⠀ ⢿⣿⣿⣿⠁⠀⠀⠀⠀⠀⣿⣿⣿⣿⠀
       ⣿ |\__/,|   (`\ ⣿⣿⠀⠀⠀⠀⠀⠀⣿⣿⣿⣿
       _.|o o  }_   ) )⣿⣿⠀⠀⠀⠀⠀⠀⣿⣿⣿⣿
    ---(((---(((-----------------⣿⣿⣿⣿

This file is an activityplugin 
which is built to store and modify TiddlyWikis - http://tiddlywiki.com
within the Moodle Learning Plattform           - http://moodle.org/

Moodle, TiddlyWiki and this plugin are all free software: you can redistribute it and/or modify
them under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Moodle is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

The original Code is copyright 2009 by Petr Skoda (http://skodak.org)
This plugin was started by JanJoh https://talk.tiddlywiki.org/u/janjo/summary

This plugin will work with the Tiddlyspot saver pointing to ../../../../../mod/tiddlywiki/store.php

=============

Tiddlywiki activity module is made starting from a clone to of the "Resource module."

Clone the ressource module
Add a clone of libdir/resourcelib.php to the directory rename it twlib.php.
grep libdir/resourcelib.php > dirroot/mod/tiddlywiki/twlib.php
gerp resource > tiddlywiki
replace pix and lang/en/tiddlywiki

TODO: 
 * add a modified version of store.php
 * get it to work with the db: 
 * store the uploaded TW in the DB
 * set the last updated db.record the main file
 * implement clean backups
 * settings to allow normal users uploading and seting the main File.
 --------------------------
 * modify the Iframe to optionally add the username to the URL
 * implement json reciever in the moodle-plugin
 * implement media-hosting rtrieving files to show in the Wiki with a media.php
