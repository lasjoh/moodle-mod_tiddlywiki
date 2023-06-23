<?php
//Settings
$DEBUG = true;				// true | false
$CLEAN_BACKUP = true; 		// during backuping a file, remove overmuch backups
$FOLD_JS = true; 			// if javascript files have been expanded during download the fold them
error_reporting(E_ERROR | E_WARNING | E_PARSE);

//GET REQUESTS only if DEBUG is true 
if (!$DEBUG) {
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
?>
<html>
	<body>
		<p align="center"> there is nothing to get here...</p>		
	</body>
</html>
<?php
exit;
}
}
// var definitions -------------------- for store.php
$uploadDir = './';
$uploadDirError = false;
$backupError = false;

// get options USER from POST
$optionStr = $_POST['UploadPlugin'];
$optionArr=explode(';',$optionStr);
$options = array();
$backupFilename = '';
$filename = $_FILES['userfile']['name'];
$destfile = $filename;

foreach($optionArr as $o) {
	list($key, $value) = explode('=', $o);
	$options[$key] = $value;
}
//retrieve the URL from the post, the uploadDir value in the save-mechanism is hijacked for this.
$url = $options['uploaddir']; 

// debug activated by client
if ($options['debug'] == 1) {
	$DEBUG = true;
}

// authentification mechanism ____________integrate Moodle capabilities!!
require_once('../../config.php');
global $USER;

if (empty($USER->id)) {
  if (include 'auth.php'){
	  if((!$options['user']) || (!$options['password']) || ($USERS[$options['user']] != $options['password'])) {
		echo "Error : UserName or Password do not match \n";
		toExit();
	  }  
  }else{
    echo "Error :you are not logged in";
	  toExit();
  }
}

//Get UserID
$userid = ($USER->id);

// parse URL to components --------also important for DEBUGGING
function parse_moodle_url($url) {
  //Get the file path from the URL
  $file_path = parse_url($url, PHP_URL_PATH);

  //Explode the path to get the parameters
  $path_parts = explode('/', $file_path);

  //Extract the parameters: 
  //component name: third segment; instance ID: fourth segment;
  //file ID: sixth segment; filename: seventh segment
  $component = $path_parts[4];
  $contextid = $path_parts[3];
  $file_id = $path_parts[6];
  $filename = end($path_parts);
  
  //Return an array with the parsed parameters
  return array(
    'component' => $component,
    'contextid' => $contextid,
    'file_id' => $file_id,
    'filename' => $filename
  );
}

//Call the function and get the parsed values in an array
$parsed = parse_moodle_url($url);

//Extract and assign each parsed value to a separate variable
$component = $parsed['component'];
$contextid = $parsed['contextid'];
$file_id = $parsed['file_id'];
$filename = $parsed['filename'];

require_once($CFG->libdir . '/accesslib.php');

/**
 * Check if the current user has minor rights in the given context.
 *
 * This function checks if the current user is an editing teacher or admin,
 * and allows them to continue. For all other users, it calls the minorrights()
 * function and returns its result.
 *
 * @param int $contextid The ID of the context to check for minor rights.
 * @param int $userid The ID of the user to check for minor rights.
 * @return bool True if the user has minor rights, false otherwise.
 */
function can_update_Wiki($contextid, $userid) {
    // Obtain the context object
    $context = context::instance_by_id($contextid);

    // Determine the course ID for the context
    if ($context->contextlevel == CONTEXT_COURSE) {
        // The context is already a course context, so just get the course ID
        $courseid = $context->instanceid;
    } else {
        // The context is a module or block context, so get the course context first
        $coursecontext = $context->get_course_context();
        $courseid = $coursecontext->instanceid;
    }

    // Determine if user is an editing teacher or admin
    $usercontext = context_user::instance($userid);
    $can_edit = has_capability('moodle/course:update', $context) || has_capability('moodle/course:manageactivities', $context) || has_capability('moodle/course:managesections', $context);
    $is_admin = is_siteadmin() || has_capability('moodle/site:config', $context);

    if ($can_edit || $is_admin) {
        return true; // allow editing teachers and admins to continue
    } else {
        // check if the user has minor rights
        $roles = get_user_roles($context, $userid);
        foreach ($roles as $role) {
            if ($role->shortname == 'editingteacher' || $role->shortname == 'teacher') {
                return true; // allow editing teachers to continue
            }
        }

        // for non-editing teachers and students, call the minorrights() function
        return minorrights($contextid, $userid);
    }
}

/**
 * Check if the current user has minor rights in the given context.
 *
 * This is just an example function that returns true if the user has
 * minor rights in the current context, and false otherwise. You would
 * need to replace this with your own function that checks for the
 * specific minor rights you want to allow.
 *
 * @param int $contextid The ID of the context to check for minor rights.
 * @param int $userid The ID of the user to check for minor rights.
 * @return bool True if the user has minor rights, false otherwise.
 */
function minorrights($contextid, $userid) {
echo "perform minorrightsactions for contextid: ".$contextid." userid: ".$userid;
}

//furnish debugging information when exit
function toExit() {
  global $DEBUG, $contextid, $userid, $filename, $file_id, $backupFilename, $options, $url, $can_update, $Update_successful;
  if ($DEBUG) {
    echo ("\nHere is some debugging info : \n");
    echo "userid: " . $userid . "\n";
    echo("\$filename : " . $filename . "\n");
    echo("\$backupFilename : " . $backupFilename . "\n");
    print ("\$_FILES : \n");
    print_r($_FILES);
    print ("\$options : \n");
    print_r($options);
	echo "posted URL: " . $url . "\n";
    echo "Context ID: " . $contextid . "\n";
    echo "File ID: " . $file_id . "\n";
    echo "Filename: " . $filename . "\n";
	echo "User can update: " . $can_update . "\n";
	echo "Update was successful: " . $Update_successful . "\n";
	echo  $_FILES['userfile'];
	var_dump($_FILES);
  }
  exit;
}


function replaceJSContentIn($content) {
	if (preg_match ("/(.*?)<!--DOWNLOAD-INSERT-FILE:\"(.*?)\"--><script\s+type=\"text\/javascript\">(.*)/ms", $content,$matches)) {
		$front = $matches[1];
		$js = $matches[2];
		$tail = $matches[3];
		if (preg_match ("/<\/script>(.*)/ms", $tail,$matches2)) {
			$tail = $matches2[1];
		}
		$jsContent = "<script type=\"text/javascript\" src=\"$js\"></script>";
		$tail = replaceJSContentIn($tail);
		return($front.$jsContent.$tail);
	}
	else
		return $content;
}
// 
// Check if file_uploads is active in php config
if (ini_get('file_uploads') != '1') {
   echo "Error : File upload is not active in php.ini\n";
   toExit();
}

if ($_FILES['error'] == UPLOAD_ERR_OK) {
	if($DEBUG) {
		echo "Debug mode \n\n";
	}
	if (!$backupError) {
		echo "0 - File successfully loaded in " .$destfile. "\n";
	} else {
		echo "BackupError : $backupError - File successfully loaded in " .$destfile. "\n";
	}
	echo("destfile:$destfile \n");
	if (($backupFilename) && (!$backupError)) {
		echo "backupfile:$backupFilename\n";
	}
	$mtime = filemtime($destfile);
	echo("mtime:$mtime");
} else {
	echo "Error : " . $_FILES['error']." - File NOT uploaded !\n";
}



function updateFileContent($contextid, $filename, $uploadedFile) {
  // Check for upload errors
  if ($uploadedFile['error'] !== UPLOAD_ERR_OK) {
    return false; // Upload error
  }

  // Load necessary libraries
  require_once($CFG->libdir . '/filelib.php');
  require_once($CFG->libdir . '/moodlelib.php');

  // Define valid extensions
  $valid_extensions = array('.txt', '.md', '.html');

  // Retrieve file record
  $fs = get_file_storage();
  $file = $fs->get_file($contextid, 'mod_tiddlywiki', 'content', 0, '/', $filename);

  if ($file) {
    // Check uploaded file extension
    $extension = strrchr($uploadedFile['name'], '.');
    if (!in_array($extension, $valid_extensions)) {
      return false; // Invalid file type
    }

    // Check filename and extension of new file
    $newfilename = $file->get_filename();
    $newextension = strrchr($newfilename, '.');
    $oldfilename = $filename;
    $oldextension = strrchr($oldfilename, '.');
    if ($newfilename != $oldfilename || $newextension != $oldextension) {
      return false; // Filename or extension do not match old file
    }

    // Delete old file
    $file->delete();

    // Save new file
    $component = 'mod_tiddlywiki'; // The name of the Moodle component
    $filearea = 'content'; // The name of the file area
    $source = $uploadedFile['tmp_name']; // The temporary file path of the uploaded file
    $filepath = '/'; // The file path you want to upload to

    // Create new file object from uploaded file
    $newfile = $fs->create_file_from_pathname([
        'contextid' => $contextid,
        'component' => $component,
        'filearea' => $filearea,
        'itemid' => 0,
        'filepath' => $filepath,
        'filename' => $newfilename
    ], $source);

    // Rename new file to replace the old one
    $newfile->rename($filename);

    return $newfile->get_data(); // Return updated file data
  } else {
    return false;
  }
}

// Extract the $_FILES['userfile'] array and pass it to the function
$result = updateFileContent($contextid, $filename, $_FILES['userfile']);

if ($result) {
  $Update_successful = true;
} else {
  $Update_successful = false;
}

toExit(); 


