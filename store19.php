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

// authentification mechanism _________integrate Moodle capabilities-system!!
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

/* can_update_Wiki:
 * This function checks if the current user is an editing teacher or admin,
 * and allows them to continue. For all other users, it calls the minorrights()
 * function and returns its result.
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

/* Check if the current user has minor rights in the given context.
 * This is just an example function that returns true if the user has
 * minor rights in the current context, and false otherwise. You would
 * need to replace this with your own function that checks for the
 * specific minor rights you want to allow.
 */
function minorrights($contextid, $userid) {
echo "perform minorrightsactions for contextid: ".$contextid." userid: ".$userid;
}

//furnish debugging information when exit
function toExit($message) {
  global $DEBUG, $contextid, $userid, $filename, $file_id, $backupFilename, $options, $url, $can_update, $Update_successful, $dbconn;
  if ($DEBUG) {
    echo ("\nHere is some debugging info : \n");
	if (is_array($message)){
    echo "<pre>" . print_r($message, true) . "</pre>";
    } else {
	echo $message;
	}
	echo "\n";
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
	echo "Pathhash: " . $pathhash . "\n";
	echo "DB Record ID: " . $db_record_id . "\n";
	echo  $_FILES['userfile'];
	var_dump($_FILES);
  }
  exit;
}

if (can_update_Wiki($contextid, $userid)) {
	$can_update = "yes"; 
    echo "you can update the wiki";
	
} else {
	$can_update = "no";
	toExit("you do not have the right to update the wiki");
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
		echo "\nDebug mode \n";
	}
	if (!$backupError) {
		echo "File successfully loaded in " .$destfile. "\n";
	} else {
		echo "BackupError : $backupError - File successfully loaded in " .$destfile. "\n";
	}
	echo("destfile:$destfile \n");
	if (($backupFilename) && (!$backupError)) {
		echo "backupfile:$backupFilename\n";
	}
	$mtime = filemtime($destfile);
	echo("mtime:$mtime\n");
} else {
	echo "Error : " . $_FILES['error']." - File NOT uploaded !\n";
}

//here we go!
// Get the uploaded file contents
$tmp_path = $_FILES['userfile']['tmp_name'];
$newcontenthash = hash_file('sha1', $tmp_path);
$file_content = file_get_contents($tmp_path);

if (isset($CFG->dataroot)) {
    $dataroot = $CFG->dataroot;
    echo "dataroot value: " . $dataroot . "\n";
} else {
    echo "Unable to retrieve dataroot value.\n";
}

// Construct the filedir folder path
$filedir_path = str_replace('/', '\\', $CFG->dataroot) . '\\filedir';

// Check if filedir folder exists
if (is_dir($filedir_path)) {
    echo "The directory filedir exists within the data root folder.\n";
} else {
    echo "The directory filedir does not exist within the data root folder.\n";
}

$first_folder = substr($newcontenthash, 0, 2);
$second_folder = substr($newcontenthash, 2, 2);

// Create the folders if they don't exist
if (!is_dir($filedir_path . '\\' . $first_folder)) {
    mkdir($filedir_path . '\\' . $first_folder, 0777, true);
}

if (!is_dir($filedir_path . '\\' . $first_folder . '\\' . $second_folder)) {
    mkdir($filedir_path . '\\' . $first_folder . '\\' . $second_folder, 0777, true);
}

// Save the file content in the created folder
$newfilepath = $filedir_path . '\\' . $first_folder . '\\' . $second_folder . '\\' . $newcontenthash;
file_put_contents($newfilepath, $file_content);

echo "File saved at: " . $newfilepath . "\n";


// Establish a database connection
$conn = mysqli_connect($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname);

// Check connection
if (!$conn) {
  toExit("Database connection failed.");
} else {
  echo "Database connection successful. \n";
}

// Get the actual table prefix from the config.php file
$prefix = $CFG->prefix;

// Start transaction
mysqli_autocommit($conn, false);

// Retrieve ID and context ID of TiddlyWiki file from Moodle files database
$sql = "SELECT id, filename, userid, timemodified FROM {$prefix}files WHERE component = 'mod_tiddlywiki' AND filename = '{$filename}' AND contextid = '{$contextid}' AND sortorder = '1'";
if (!$result = mysqli_query($conn, $sql)) {
  mysqli_rollback($conn);
  toExit("Query failed: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) == 0) {
  mysqli_rollback($conn);
  $errorMsg = "No results found for filename '{$filename}' and contextid '{$contextid}'.<br>";
} else {
  // Get the ID, context ID, filepath and filename of the TiddlyWiki file
  $row = mysqli_fetch_assoc($result);
  $id = $row['id'];
  $thatuserid = $row['userid'];
  $thattime = $row['timemodified'];
  echo '$thatuserid:'.$thatuserid."\n";
  echo '$thattime:'.$thattime."\n";
  $thattime = date("Y-m-d_H:i:s", $thattime);
  echo '$thattime:'.$thattime."\n";

// Fetch the current record
$sql = "SELECT * FROM mdl_files WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
  toExit("Failed to prepare select statement: " . mysqli_error($conn));
}
if (!mysqli_stmt_bind_param($stmt, 'i', $id)) {
  mysqli_stmt_close($stmt);
  toExit("Failed to bind parameter for select statement: " . mysqli_error($conn));
}
if (!mysqli_stmt_execute($stmt)) {
  mysqli_stmt_close($stmt);
  toExit("Failed to execute select statement: " . mysqli_error($conn));
}

// Fetch the result set
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

// Print all keys and values
foreach ($row as $key => $value) {
  echo "$key: $value\n";
}

$newfilesize = $_FILES['userfile']['size'];
$current_time = time(); // Replace with the appropriate logic to get the current time
$sql = "UPDATE {$prefix}files SET filesize = ?, contenthash = ?, userid = ?, timemodified = ? WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    mysqli_rollback($conn);
    exit("Prepare failed: " . mysqli_error($conn));
}

if (!mysqli_stmt_bind_param($stmt, 'issii', $newfilesize, $newcontenthash, $userid, $current_time, $id)) {
    mysqli_stmt_close($stmt);
    exit("Failed to bind parameters for update statement: " . mysqli_error($conn));
}

if (!mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    exit("Failed to execute update statement: " . mysqli_error($conn));
}

mysqli_stmt_close($stmt);

//Get the name $thatuser who saved the the file 
$sql = "SELECT username
        FROM {user}
        WHERE id = :userid";
$params = array('userid' => $thatuserid);
$thatuser = $DB->get_record_sql($sql, $params);
if ($thatuser) {
    $thatuser = $thatuser->username;
} else {
    $thatuser = ''; // User not found, assign default value or handle accordingly
}
echo '$thatuser:'.$thatuser."\n";
$filepath = "/".$options['backupDir']."/";
echo '$filepath:'.$filepath."\n";
$filename = $thatuser."_".$thattime ;
echo '$filename:'.$filename."\n";

// Check for error
if (mysqli_error($conn)) {
  mysqli_rollback($conn);
  toExit("Update failed: " . mysqli_error($conn));
}
  // Commit transaction
  mysqli_commit($conn);
}
// Check if there are any errors
if (isset($errorMsg)) {
  toExit($errorMsg);
}

// Close the database connection
mysqli_close($conn);
toExit("the PHP reached the end!");
