<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "kk";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Update
if (isset($_POST['update_user'])) {
    $id = intval($_POST['id']);
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $photo = $conn->real_escape_string($_POST['photo']);

    $conn->query("UPDATE users SET name='$name', email='$email', photo='$photo' WHERE id=$id");
    echo "<p style='color:green;'>User ID $id updated successfully.</p>";
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM users WHERE id=$id");
    echo "<p style='color:red;'>User ID $id deleted successfully.</p>";
}

// Fetch users
$result = $conn->query("SELECT * FROM users");
if (!$result) {
    die("Query failed: " . $conn->error);
}

echo "<h2>User Table</h2>";

if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse:collapse;'>";
    echo "<tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Photo</th>
            <th>Actions</th>
        </tr>";

    while ($row = $result->fetch_assoc()) {
        if (isset($_GET['edit']) && $_GET['edit'] == $row['id']) {
            // Edit mode row
            echo "<form method='POST'>
            <tr>
                <td>{$row['id']}<input type='hidden' name='id' value='{$row['id']}'></td>
                <td><input type='text' name='name' value='" . htmlspecialchars($row['name']) . "'></td>
                <td><input type='email' name='email' value='" . htmlspecialchars($row['email']) . "'></td>
                <td><input type='text' name='photo' value='" . htmlspecialchars($row['photo']) . "'></td>
                <td>
                    <input type='submit' name='update_user' value='Update'
                        style='background:#28a745; color:#fff; border:none; padding:5px 10px; border-radius:3px;'>
                    <a href='manage_users.php' 
                        style='background:#6c757d; color:#fff; padding:5px 10px; text-decoration:none; border-radius:3px; margin-left:3px;'>Cancel</a>
                </td>
            </tr>
            </form>";
        } else {
            // Normal display row
            echo "<tr>
                <td>{$row['id']}</td>
                <td>" . htmlspecialchars($row['name']) . "</td>
                <td>" . htmlspecialchars($row['email']) . "</td>
                <td><img src='" . htmlspecialchars($row['photo']) . "' width='50' alt='photo'></td>
                <td>
                    <a href='manage_users.php?edit={$row['id']}'
                       style='background:#007bff; color:#fff; padding:4px 10px; text-decoration:none; border-radius:3px;'>Edit</a>
                    &nbsp;
                    <a href='manage_users.php?delete={$row['id']}'
                        style='background:#dc3545; color:#fff; padding:4px 10px; text-decoration:none; border-radius:3px;'
                        onclick=\"return confirm('Are you sure you want to delete this user?')\">Delete</a>
                </td>
              </tr>";
        }
    }

    echo "</table>";
} else {
    echo "No users found.";
}

$conn->close();
?>
