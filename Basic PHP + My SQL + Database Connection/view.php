<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "kk";

// Connect to the database
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "<h2>Connected successfully</h2>";

// Get all tables
$tables = $conn->query("SHOW TABLES");

if ($tables->num_rows > 0) {
    while ($table = $tables->fetch_array()) {
        $tableName = $table[0];
        echo "<h3>Table: $tableName</h3>";

        // Get all data from the table
        $result = $conn->query("SELECT * FROM `$tableName`");

        if ($result->num_rows > 0) {
            echo "<table border='1' cellpadding='5' cellspacing='0'>";
            
            // Print column names
            echo "<tr>";
            while ($field = $result->fetch_field()) {
                echo "<th>{$field->name}</th>";
            }
            echo "</tr>";

            // Print rows
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($row as $value) {
                    echo "<td>$value</td>";
                }
                echo "</tr>";
            }

            echo "</table><br>";
        } else {
            echo "No data found in $tableName<br>";
        }
    }
} else {
    echo "No tables found in the database.";
}

$conn->close();
?>
