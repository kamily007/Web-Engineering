<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "marriage_biodata_db";

$message = "";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Collect form data
        $full_name = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
        $date_of_birth = isset($_POST['dob']) ? $_POST['dob'] : '';
        $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
        $height = isset($_POST['height']) ? trim($_POST['height']) : '';
        $religion = isset($_POST['religion']) ? $_POST['religion'] : '';
        $nationality = isset($_POST['nationality']) ? trim($_POST['nationality']) : 'Bangladeshi';
        $marital_status = isset($_POST['marital']) ? $_POST['marital'] : '';
        $education = isset($_POST['education']) ? trim($_POST['education']) : '';
        $present_address = isset($_POST['presentAddress']) ? trim($_POST['presentAddress']) : '';
        $permanent_address = isset($_POST['permanentAddress']) ? trim($_POST['permanentAddress']) : '';
        $contact_number = isset($_POST['contactNumber']) ? trim($_POST['contactNumber']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        
        // Family details
        $father_name = '';
        $father_occupation = '';
        if (isset($_POST['fatherName']) && !empty($_POST['fatherName'])) {
            $father_info = explode(',', $_POST['fatherName'], 2);
            $father_name = isset($father_info[0]) ? trim($father_info[0]) : '';
            $father_occupation = isset($father_info[1]) ? trim($father_info[1]) : '';
        }
        
        $mother_name = '';
        $mother_occupation = '';
        if (isset($_POST['motherName']) && !empty($_POST['motherName'])) {
            $mother_info = explode(',', $_POST['motherName'], 2);
            $mother_name = isset($mother_info[0]) ? trim($mother_info[0]) : '';
            $mother_occupation = isset($mother_info[1]) ? trim($mother_info[1]) : '';
        }
        
        $siblings = isset($_POST['siblings']) ? trim($_POST['siblings']) : '';
        $family_type = isset($_POST['familyType']) ? $_POST['familyType'] : '';
        
        // Handle hobbies
        $hobbies = '';
        if (isset($_POST['hobbies']) && is_array($_POST['hobbies'])) {
            $hobbies = implode(', ', $_POST['hobbies']);
        }
        
        // Handle file upload
        $profile_photo = '';
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
            $target_dir = "uploads/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $file_extension = strtolower(pathinfo($_FILES["profile_photo"]["name"], PATHINFO_EXTENSION));
            $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
            if (in_array($file_extension, $allowed_types)) {
                $new_filename = uniqid('profile_') . '.' . $file_extension;
                $profile_photo = $target_dir . $new_filename;
                move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $profile_photo);
            }
        }
        
        // Validate required fields
        if (empty($full_name) || empty($date_of_birth) || empty($gender) || empty($religion) || empty($marital_status)) {
            throw new Exception("Please fill in all required fields.");
        }
        
        // Insert data into database
        $sql = "INSERT INTO biodata (
            full_name, date_of_birth, gender, height, religion, nationality, 
            marital_status, education, present_address, permanent_address, 
            contact_number, email, profile_photo, father_name, father_occupation,
            mother_name, mother_occupation, siblings, family_type, hobbies
        ) VALUES (
            :full_name, :date_of_birth, :gender, :height, :religion, :nationality,
            :marital_status, :education, :present_address, :permanent_address,
            :contact_number, :email, :profile_photo, :father_name, :father_occupation,
            :mother_name, :mother_occupation, :siblings, :family_type, :hobbies
        )";
        
        $stmt = $pdo->prepare($sql);
        
        $result = $stmt->execute([
            ':full_name' => $full_name,
            ':date_of_birth' => $date_of_birth,
            ':gender' => $gender,
            ':height' => $height,
            ':religion' => $religion,
            ':nationality' => $nationality,
            ':marital_status' => $marital_status,
            ':education' => $education,
            ':present_address' => $present_address,
            ':permanent_address' => $permanent_address,
            ':contact_number' => $contact_number,
            ':email' => $email,
            ':profile_photo' => $profile_photo,
            ':father_name' => $father_name,
            ':father_occupation' => $father_occupation,
            ':mother_name' => $mother_name,
            ':mother_occupation' => $mother_occupation,
            ':siblings' => $siblings,
            ':family_type' => $family_type,
            ':hobbies' => $hobbies
        ]);
        
        if ($result) {
            $message = "<div style='color: green; text-align: center; margin: 20px; padding: 15px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; font-weight: bold;'>✅ Biodata submitted successfully! Record ID: " . $pdo->lastInsertId() . "</div>";
        }
        
    } catch(Exception $e) {
        $message = "<div style='color: red; text-align: center; margin: 20px; padding: 15px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; font-weight: bold;'>❌ Error: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Marriage Biodata</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f5f7fa;
      padding: 30px;
    }

    .biodata-container {
      max-width: 800px;
      margin: auto;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      padding: 30px;
    }

    .photo-section {
      text-align: center;
      margin-bottom: 20px;
    }

    .profile-pic {
      width: 150px;
      height: 150px;
      object-fit: cover;
      border-radius: 50%;
      border: 4px solid #2c3e50;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    h2 {
      text-align: center;
      color: #2c3e50;
      margin-bottom: 20px;
    }

    .section {
      margin-bottom: 25px;
    }

    .section-title {
      background: #2c3e50;
      color: #fff;
      padding: 10px 15px;
      border-radius: 6px;
      font-size: 18px;
      margin-bottom: 10px;
    }

    .info-grid {
      display: grid;
      grid-template-columns: 1fr 2fr;
      gap: 8px 20px;
      align-items: center;
    }

    .info-grid div {
      padding: 6px 0;
    }

    input[type="text"],
    input[type="email"],
    input[type="tel"],
    input[type="date"],
    input[type="file"],
    select {
      width: 90%;
      padding: 6px 8px;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 14px;
    }

    label {
      margin-right: 15px;
      cursor: pointer;
    }

    .hobbies {
      padding-left: 20px;
      list-style: none;
    }

    .hobbies li {
      margin-bottom: 6px;
    }

    .submit-btn {
      text-align: center;
      margin-top: 30px;
    }

    button {
      background-color: #2c3e50;
      color: white;
      border: none;
      padding: 12px 30px;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
    }

    button:hover {
      background-color: #405a75;
    }

    .view-database-btn {
      text-align: center;
      margin-bottom: 20px;
    }

    .view-database-btn a {
      background-color: #28a745;
      color: white;
      text-decoration: none;
      padding: 10px 25px;
      border-radius: 6px;
      font-size: 14px;
    }

    .view-database-btn a:hover {
      background-color: #218838;
    }
  </style>
</head>
<body>
  <div class="biodata-container">
    
    <!-- View Database Button -->
    <div class="view-database-btn">
      <a href="view_database.php">👁️ View All Database Records</a>
    </div>

    <?php echo $message; ?>
    
    <!-- Profile Picture Section -->
    <div class="photo-section">
      <img src="WhatsApp Image 2025-07-14 at 22.23.55_a2362fd0.jpg" alt="Profile Photo" class="profile-pic" />
    </div>

    <h2>Biodata</h2>
    
    <!-- Form submits to the same page -->
    <form action="" method="POST" enctype="multipart/form-data">
      <div class="section">
        <div class="section-title">Personal Information</div>
        <div class="info-grid">
          <div><label for="fullname">Full Name:</label></div>
          <div><input type="text" id="fullname" name="fullname" required /></div>

          <div><label for="dob">Date of Birth:</label></div>
          <div><input type="date" id="dob" name="dob" required /></div>

          <div>Gender:</div>
          <div>
            <label><input type="radio" name="gender" value="Female" required /> Female</label>
            <label><input type="radio" name="gender" value="Male" required /> Male</label>
          </div>

          <div><label for="height">Height:</label></div>
          <div><input type="text" id="height" name="height" placeholder="5'3&quot;" /></div>

          <div><label for="religion">Religion:</label></div>
          <div>
            <select id="religion" name="religion" required>
              <option value="">Select Religion</option>
              <option value="Islam">Islam</option>
              <option value="Hinduism">Hinduism</option>
              <option value="Christianity">Christianity</option>
              <option value="Other">Other</option>
            </select>
          </div>

          <div><label for="nationality">Nationality:</label></div>
          <div><input type="text" id="nationality" name="nationality" value="Bangladeshi" /></div>

          <div>Marital Status:</div>
          <div>
            <label><input type="radio" name="marital" value="Never Married" required /> Never Married</label>
            <label><input type="radio" name="marital" value="Married" required /> Married</label>
          </div>

          <div><label for="education">Education:</label></div>
          <div><input type="text" id="education" name="education" placeholder="Your education qualification" /></div>

          <div><label for="presentAddress">Present Address:</label></div>
          <div><input type="text" id="presentAddress" name="presentAddress" placeholder="Current address" /></div>

          <div><label for="permanentAddress">Permanent Address:</label></div>
          <div><input type="text" id="permanentAddress" name="permanentAddress" placeholder="Permanent address" /></div>

          <div><label for="contactNumber">Contact Number:</label></div>
          <div><input type="tel" id="contactNumber" name="contactNumber" placeholder="Phone number" /></div>

          <div><label for="email">Email ID:</label></div>
          <div><input type="email" id="email" name="email" placeholder="Email address" /></div>

          <div><label for="profile_photo">Upload Photo:</label></div>
          <div><input type="file" id="profile_photo" name="profile_photo" accept="image/*" /></div>
        </div>
      </div>

      <div class="section">
        <div class="section-title">Family Details</div>
        <div class="info-grid">
          <div><label for="fatherName">Father's Name & Occupation:</label></div>
          <div><input type="text" id="fatherName" name="fatherName" placeholder="Name, Occupation" /></div>

          <div><label for="motherName">Mother's Name & Occupation:</label></div>
          <div><input type="text" id="motherName" name="motherName" placeholder="Name, Occupation" /></div>

          <div><label for="siblings">Siblings:</label></div>
          <div><input type="text" id="siblings" name="siblings" placeholder="Brothers and sisters information" /></div>

          <div>Family Type:</div>
          <div>
            <label><input type="radio" name="familyType" value="Joint" /> Joint Family</label>
            <label><input type="radio" name="familyType" value="Nuclear" /> Nuclear Family</label>
          </div>
        </div>
      </div>

      <div class="section">
        <div class="section-title">Hobbies & Interests</div>
        <ul class="hobbies">
          <li><label><input type="checkbox" name="hobbies[]" value="Reading" /> Reading</label></li>
          <li><label><input type="checkbox" name="hobbies[]" value="Travelling" /> Travelling</label></li>
          <li><label><input type="checkbox" name="hobbies[]" value="Spending time with family" /> Spending time with family</label></li>
          <li><label><input type="checkbox" name="hobbies[]" value="Cooking" /> Cooking</label></li>
          <li><label><input type="checkbox" name="hobbies[]" value="Gardening" /> Gardening</label></li>
          <li><label><input type="checkbox" name="hobbies[]" value="Sports" /> Sports</label></li>
          <li><label><input type="checkbox" name="hobbies[]" value="Music" /> Music</label></li>
          <li><label><input type="checkbox" name="hobbies[]" value="Movies" /> Movies</label></li>
        </ul>
      </div>

      <div class="submit-btn">
        <button type="submit">Submit Biodata</button>
      </div>
    </form>
  </div>
</body>
</html>
