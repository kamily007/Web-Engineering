<?php
session_start();
$login_error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $image = $_FILES['image']['name'];

    // Database connection
    $conn = new mysqli("localhost", "root", "", "biodata_db");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Validate user based on name and image filename
    $stmt = $conn->prepare("SELECT * FROM users WHERE name = ? AND image_name = ?");
    $stmt->bind_param("ss", $name, $image);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['user'] = $name;
        header("Location: index.php");
        exit;
    } else {
        $login_error = "Invalid name or image.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login with Image Verification</title>
  <style>
    body {
      font-family: Arial;
      background: #f0f0f0;
      padding: 30px;
    }
    .login-box {
      max-width: 400px;
      margin: auto;
      background: white;
      padding: 25px;
      border-radius: 8px;
      box-shadow: 0 0 10px #ccc;
    }
    input[type="text"], input[type="file"] {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
    }
    button {
      background-color: #2c3e50;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }
    .error {
      color: red;
    }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>Login</h2>
    <?php if ($login_error): ?>
      <p class="error"><?= $login_error ?></p>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
      <label for="name">Enter Your Name:</label>
      <input type="text" id="name" name="name" required>

      <label for="image">Upload Your Profile Image:</label>
      <input type="file" id="image" name="image" accept="image/*" required>

      <button type="submit">Login</button>
    </form>
  </div>
</body>
</html>
