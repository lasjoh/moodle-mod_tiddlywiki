<?php
//Settings
$DEBUG = true;				// true | false
$CLEAN_BACKUP = true; 		// during backuping a file, remove overmuch backups
$FOLD_JS = true; 			// if javascript files have been expanded during download the fold them
error_reporting(E_ERROR | E_WARNING | E_PARSE);

/*GET Request killer----------------deactivated for testing!
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
*/
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
  $instance_id = $path_parts[3];
  $file_id = $path_parts[6];
  $filename = end($path_parts);

  //Return an object with the parsed parameters
  return (object)array(
    'component' => $component,
    'instance_id' => $instance_id,
    'file_id' => $file_id,
    'filename' => $filename
  );
}
//furnish debugging information when exit
function toExit() {
	global $DEBUG, $filename, $backupFilename, $options, $url;
	if ($DEBUG) {
		echo ("\nHere is some debugging info : \n");
		echo("\$filename : $filename \n");
		echo("\$backupFilename : $backupFilename \n");
		print ("\$_FILES : \n");
		print_r($_FILES);
		print ("\$options : \n");
		print_r($options);
		$parsed_url = parse_moodle_url($url);
echo "Component: " . $parsed_url->component . "\n";
echo "Instance ID: " . $parsed_url->instance_id . "\n";
echo "File ID: " . $parsed_url->file_id . "\n";
echo "Filename: " . $parsed_url->filename . "\n";
echo "URL: " . $url . "\n";
	}
	exit;
}
//to be removed for saving in db -->
// Recursive mkdir
function mkdirs($dir) {
	if( is_null($dir) || $dir === "" ){
		return false;
	}
	if( is_dir($dir) || $dir === "/" ){
		return true;
	}
	if( mkdirs(dirname($dir)) ){
		return mkdir($dir);
	}
	return false;
}

function ParseTWFileDate($s) {
	// parse date element
	preg_match ( '/^(\d\d\d\d)(\d\d)(\d\d)\.(\d\d)(\d\d)(\d\d)/', $s , $m );
	// make a date object
	$d = mktime($m[4], $m[5], $m[6], $m[2], $m[3], $m[1]);
	// get the week number
	$w = date("W",$d);

	return array(
		'year' => $m[1],
		'mon' => $m[2],
		'mday' => $m[3],
		'hours' => $m[4],
		'minutes' => $m[5],
		'seconds' => $m[6],
		'week' => $w);
}

function cleanFiles($dirname, $prefix) {
	$now = getdate();
	$now['week'] = date("W");

	$hours = Array();
	$mday = Array();
	$year = Array();

	$toDelete = Array();

	// need files recent first
	$files = Array();
	($dir = opendir($dirname)) || die ("can't open dir '$dirname'");
	while (false !== ($file = readdir($dir))) {
		if (preg_match("/^$prefix/", $file))
        array_push($files, $file);
    }
	$files = array_reverse($files);

	// decides for each file
	foreach ($files as $file) {
		$fileTime = ParseTWFileDate(substr($file,strpos($file, '.')+1,strrpos($file,'.') - strpos($file, '.') -1));
		if (($now['year'] == $fileTime['year']) &&
			($now['mon'] == $fileTime['mon']) &&
			($now['mday'] == $fileTime['mday']) &&
			($now['hours'] == $fileTime['hours']))
				continue;
		elseif (($now['year'] == $fileTime['year']) &&
			($now['mon'] == $fileTime['mon']) &&
			($now['mday'] == $fileTime['mday'])) {
				if (isset($hours[$fileTime['hours']]))
					array_push($toDelete, $file);
				else
					$hours[$fileTime['hours']] = true;
			}
		elseif 	(($now['year'] == $fileTime['year']) &&
			($now['mon'] == $fileTime['mon'])) {
				if (isset($mday[$fileTime['mday']]))
					array_push($toDelete, $file);
				else
					$mday[$fileTime['mday']] = true;
			}
		else {
			if (isset($year[$fileTime['year']][$fileTime['mon']]))
				array_push($toDelete, $file);
			else
				$year[$fileTime['year']][$fileTime['mon']] = true;
		}
	}
	return $toDelete;
}
//<--to be removed for saving in db --

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

// authentication mechanism _____________integrate Moodle capabilities
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

/*Code for saving in the DB --->
function get_file_info_filepath($file_record) {
  return dirname($file_record->hash) . '/' . basename($file_record->hash, '.' . $file_record->hash);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $new_file_location = $_POST['new_file_location'];
  $parsed_url = parse_moodle_url($url);

  // Retrieve the record from the database based on the parsed URL parameters
  $sql = "SELECT * FROM {files} WHERE component = :component AND instance = :instance_id AND filearea = 'content' AND id = :file_id";
  $params = array('component' => $parsed_url->component, 'instance_id' => $parsed_url->instance_id, 'file_id' => $parsed_url->file_id);
  $result = $DB->get_record_sql($sql, $params);

  if ($result) {
    $row = (object)$result;          // Cast the result as an object
    $file_record = json_decode($row->contenthash);    // decode the JSON contenthash field

    // Next, update the record with the new version of the file
    $filepath = get_file_info_filepath($file_record);
    $fs = get_file_storage();
    $file = $fs->get_file_by_hash($file_record->hash);

    // Check if the file exists in the database
    if ($file) {
      $file->delete();                // delete the old file
    }

    // Finally, upload the new file and update the database record
    $new_file = new stdClass();
    $new_file->component = $parsed_url->component;
    $new_file->filearea = 'content';
    $new_file->itemid = $row->itemid;
    $new_file->filename = $parsed_url->filename;
    $new_file->filepath = '/';
    $new_file->contextid = $row->contextid;
    $new_file->userid = $row->userid;
    $new_file->filecontent = '';
    $new_file->filesize = filesize($new_file_location);
    $new_file->timecreated = time();
    $new_file->timemodified = $new_file->timecreated;
    $new_file->mimetype = mime_content_type($new_file_location);

    // Save the new file
    $new_file->id = $fs->create_file_from_pathname($new_file, $new_file_location);

    // Update the contenthash field in the database
    $new_file_record = array('key' => 'file', 'hash' => $new_file->get_contenthash());
    $result->contenthash = json_encode($new_file_record);
    $DB->update_record('files', $result);
    echo 'File updated successfully!';
  } else {
    echo 'File not found!';
  }
}
// Code for saving in the DB <--- --->php-foldersaver - will be removed
*/
$uploadkey = $options['uploaddir'];
$uploadDir = './';

$destfile = $uploadDir . $filename;

// backup existing file
if (file_exists($destfile) && ($options['backupDir'])) {
	if (! is_dir($options['backupDir'])) {
		mkdirs($options['backupDir']);
		if (! is_dir($options['backupDir'])) {
			$backupError = "backup mkdir error";
		}
	}
	$backupFilename = $options['backupDir'].'/'.substr($filename, 0, strrpos($filename, '.'))
				.date('.Ymd.His').substr($filename,strrpos($filename,'.'));
	rename($destfile, $backupFilename) or ($backupError = "rename error");
	// remove overmuch backup
	if ($CLEAN_BACKUP) {
		$toDelete = cleanFiles($options['backupDir'], substr($filename, 0, strrpos($filename, '.')));
		foreach ($toDelete as $file) {
			$f = $options['backupDir'].'/'.$file;
			if($DEBUG) {
				echo "delete : ".$options['backupDir'].'/'.$file."\n";
			}
			unlink($options['backupDir'].'/'.$file);
		}
	}
}

// move uploaded file to uploadDir
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $destfile)) {
	if ($FOLD_JS) {
		// rewrite the file to replace JS content
		$fileContent = file_get_contents ($destfile);
		$fileContent = replaceJSContentIn($fileContent);
		if (!$handle = fopen($destfile, 'w')) {
	         echo "Cannot open file ($destfile)";
	         exit;
	    }
	    if (fwrite($handle, $fileContent) === FALSE) {
	        echo "Cannot write to file ($destfile)";
	        exit;
	    }
	    fclose($handle);
	}

	chmod($destfile, 0644);
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
}
else {
	echo "Error : " . $_FILES['error']." - File NOT uploaded !\n";

}
toExit();