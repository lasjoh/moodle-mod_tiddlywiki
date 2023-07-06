<?php
// Include Moodle config file
require_once('../../config.php');

// Require Moodle login
require_login();

// Check if user is an administrator
if (!is_siteadmin()) {
    error("Only administrators can access this page");
}

// Define MySQL connection variables
$servername = $CFG->dbhost;
$username = $CFG->dbuser;
$password = $CFG->dbpass;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['database'])) {
    // Create MySQL connection
    $conn = mysqli_connect($servername, $username, $password);

    // Check for connection errors
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    } else {
        echo "Connected successfully to DB:".($_POST['database'])." table:".($_POST['table'])."<br>";
    }

    // Select database
    mysqli_select_db($conn, $_POST['database']);

    if (isset($_POST['table'])) {
        // Retrieve table schema
        $schema_sql = "DESCRIBE " . $_POST['table'];
        $schema_result = mysqli_query($conn, $schema_sql);

        // Output table schema as a table
        echo "<table><tr>";
        while ($column = mysqli_fetch_assoc($schema_result)) {
            echo "<th>" . $column['Field'] . "</th>";
        }
        echo "</tr>";

        // Retrieve table records
        $records_sql = "SELECT * FROM " . $_POST['table'];
        $records_result = mysqli_query($conn, $records_sql);

        // Output table records as rows in the table
        while ($record = mysqli_fetch_assoc($records_result)) {
            echo "<tr>";
            foreach ($record as $value) {
                echo "<td>" . $value . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        // Get list of tables
        $tables_sql = "SHOW TABLES";
        $tables_result = mysqli_query($conn, $tables_sql);

        // Output tables as buttons
        while ($table = mysqli_fetch_row($tables_result)) {
            echo "<form method=\"POST\"><input type=\"hidden\" name=\"database\" value=\"" . $_POST['database'] . "\"><button type=\"submit\" name=\"table\" value=\"" . $table[0] ."\">" . $table[0] . "</button></form>";
        }
    }

    // Close MySQL connection
    mysqli_close($conn);

} else {
    // Create MySQL connection
    $conn = mysqli_connect($servername, $username, $password);

    // Check for connection errors
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    } else {
        echo "Connected successfully<br>";
    }

    // Get list of databases
    $databases_sql = "SHOW DATABASES";
    $databases_result = mysqli_query($conn, $databases_sql);

    // Output databases as buttons
    while ($database = mysqli_fetch_row($databases_result)) {
        echo "<form method=\"POST\"><button type=\"submit\" name=\"database\" value=\"" . $database[0] . "\">" . $database[0] . "</button></form>";
    }

    // Close MySQL connection
    mysqli_close($conn);
}
