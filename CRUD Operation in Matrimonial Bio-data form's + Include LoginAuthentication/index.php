<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "marriage_biodata_db";

$message = "";
$edit_mode = false;
$edit_data = [];

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle DELETE operation
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    try {
        $delete_id = $_GET['delete'];
        
        // Get the photo path before deleting
        $stmt = $pdo->prepare("SELECT profile_photo FROM biodata WHERE id = :id");
        $stmt->execute([':id' => $delete_id]);
        $photo_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Delete the record
        $stmt = $pdo->prepare("DELETE FROM biodata WHERE id = :id");
        $stmt->execute([':id' => $delete_id]);
        
        // Delete the photo file if it exists
        if ($photo_data && $photo_data['profile_photo'] && file_exists($photo_data['profile_photo'])) {
            unlink($photo_data['profile_photo']);
        }
        
        $message = "<div style='color: green; text-align: center; margin: 20px; padding: 15px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; font-weight: bold;'>✅ Record deleted successfully!</div>";
    } catch(Exception $e) {
        $message = "<div style='color: red; text-align: center; margin: 20px; padding: 15px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; font-weight: bold;'>❌ Error deleting record: " . $e->getMessage() . "</div>";
    }
}

// Handle EDIT mode - Load existing data
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    try {
        $edit_id = $_GET['edit'];
        $stmt = $pdo->prepare("SELECT * FROM biodata WHERE id = :id");
        $stmt->execute([':id' => $edit_id]);
        $edit_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($edit_data) {
            $edit_mode = true;
        } else {
            $message = "<div style='color: red; text-align: center; margin: 20px; padding: 15px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; font-weight: bold;'>❌ Record not found!</div>";
        }
    } catch(Exception $e) {
        $message = "<div style='color: red; text-align: center; margin: 20px; padding: 15px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; font-weight: bold;'>❌ Error loading record: " . $e->getMessage() . "</div>";
    }
}

// Process form submission (INSERT or UPDATE)
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
        $profile_photo = isset($_POST['existing_photo']) ? $_POST['existing_photo'] : '';
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
            $target_dir = "uploads/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $file_extension = strtolower(pathinfo($_FILES["profile_photo"]["name"], PATHINFO_EXTENSION));
            $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
            if (in_array($file_extension, $allowed_types)) {
                // Delete old photo if updating
                if (!empty($profile_photo) && file_exists($profile_photo)) {
                    unlink($profile_photo);
                }
                $new_filename = uniqid('profile_') . '.' . $file_extension;
                $profile_photo = $target_dir . $new_filename;
                move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $profile_photo);
            }
        }
        
        // Validate required fields
        if (empty($full_name) || empty($date_of_birth) || empty($gender) || empty($religion) || empty($marital_status)) {
            throw new Exception("Please fill in all required fields.");
        }
        
        // UPDATE or INSERT
        if (isset($_POST['update_id']) && is_numeric($_POST['update_id'])) {
            // UPDATE existing record
            $update_id = $_POST['update_id'];
            $sql = "UPDATE biodata SET 
                full_name = :full_name, date_of_birth = :date_of_birth, gender = :gender, 
                height = :height, religion = :religion, nationality = :nationality, 
                marital_status = :marital_status, education = :education, 
                present_address = :present_address, permanent_address = :permanent_address, 
                contact_number = :contact_number, email = :email, profile_photo = :profile_photo, 
                father_name = :father_name, father_occupation = :father_occupation,
                mother_name = :mother_name, mother_occupation = :mother_occupation, 
                siblings = :siblings, family_type = :family_type, hobbies = :hobbies,
                updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";
            
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                ':id' => $update_id,
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
                $message = "<div style='color: green; text-align: center; margin: 20px; padding: 15px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; font-weight: bold;'>✅ Biodata updated successfully! Record ID: $update_id</div>";
                $edit_mode = false;
                $edit_data = [];
            }
        } else {
            // INSERT new record
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
      margin: 0 5px;
    }

    .view-database-btn a:hover {
      background-color: #218838;
    }

    .cancel-btn {
      background-color: #6c757d !important;
    }

    .cancel-btn:hover {
      background-color: #545b62 !important;
    }

    .edit-mode-header {
      background: #ffc107;
      color: #212529;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
      text-align: center;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="biodata-container">
    
    <!-- View Database Button -->
    <div class="view-database-btn">
      <a href="view_database.php">👁️ View All Database Records</a>
      <?php if ($edit_mode): ?>
        <a href="index.php" class="cancel-btn">❌ Cancel Edit</a>
      <?php endif; ?>
    </div>

    <?php if ($edit_mode): ?>
      <div class="edit-mode-header">
        📝 EDIT MODE - Updating Record ID: <?php echo $edit_data['id']; ?>
      </div>
    <?php endif; ?>

    <?php echo $message; ?>
    
    <!-- Profile Picture Section -->
    <div class="photo-section">
      <?php if ($edit_mode && $edit_data['profile_photo'] && file_exists($edit_data['profile_photo'])): ?>
        <img src="<?php echo $edit_data['profile_photo']; ?>" alt="Profile Photo" class="profile-pic" />
      <?php else: ?>
        <img src="WhatsApp Image 2025-07-14 at 22.23.55_a2362fd0.jpg" alt="Profile Photo" class="profile-pic" />
      <?php endif; ?>
    </div>

    <h2><?php echo $edit_mode ? 'Update Biodata' : 'Biodata'; ?></h2>
    
    <!-- Form submits to the same page -->
    <form action="" method="POST" enctype="multipart/form-data">
      <?php if ($edit_mode): ?>
        <input type="hidden" name="update_id" value="<?php echo $edit_data['id']; ?>" />
        <input type="hidden" name="existing_photo" value="<?php echo $edit_data['profile_photo']; ?>" />
      <?php endif; ?>
      
      <div class="section">
        <div class="section-title">Personal Information</div>
        <div class="info-grid">
          <div><label for="fullname">Full Name:</label></div>
          <div><input type="text" id="fullname" name="fullname" value="<?php echo $edit_mode ? htmlspecialchars($edit_data['full_name']) : ''; ?>" required /></div>

          <div><label for="dob">Date of Birth:</label></div>
          <div><input type="date" id="dob" name="dob" value="<?php echo $edit_mode ? $edit_data['date_of_birth'] : ''; ?>" required /></div>

          <div>Gender:</div>
          <div>
            <label><input type="radio" name="gender" value="Female" <?php echo ($edit_mode && $edit_data['gender'] == 'Female') ? 'checked' : ''; ?> required /> Female</label>
            <label><input type="radio" name="gender" value="Male" <?php echo ($edit_mode && $edit_data['gender'] == 'Male') ? 'checked' : ''; ?> required /> Male</label>
          </div>

          <div><label for="height">Height:</label></div>
          <div><input type="text" id="height" name="height" value="<?php echo $edit_mode ? htmlspecialchars($edit_data['height']) : ''; ?>" placeholder="5'3&quot;" /></div>

          <div><label for="religion">Religion:</label></div>
          <div>
            <select id="religion" name="religion" required>
              <option value="">Select Religion</option>
              <option value="Islam" <?php echo ($edit_mode && $edit_data['religion'] == 'Islam') ? 'selected' : ''; ?>>Islam</option>
              <option value="Hinduism" <?php echo ($edit_mode && $edit_data['religion'] == 'Hinduism') ? 'selected' : ''; ?>>Hinduism</option>
              <option value="Christianity" <?php echo ($edit_mode && $edit_data['religion'] == 'Christianity') ? 'selected' : ''; ?>>Christianity</option>
              <option value="Other" <?php echo ($edit_mode && $edit_data['religion'] == 'Other') ? 'selected' : ''; ?>>Other</option>
            </select>
          </div>

          <div><label for="nationality">Nationality:</label></div>
          <div><input type="text" id="nationality" name="nationality" value="<?php echo $edit_mode ? htmlspecialchars($edit_data['nationality']) : 'Bangladeshi'; ?>" /></div>

          <div>Marital Status:</div>
          <div>
            <label><input type="radio" name="marital" value="Never Married" <?php echo ($edit_mode && $edit_data['marital_status'] == 'Never Married') ? 'checked' : ''; ?> required /> Never Married</label>
            <label><input type="radio" name="marital" value="Married" <?php echo ($edit_mode && $edit_data['marital_status'] == 'Married') ? 'checked' : ''; ?> required /> Married</label>
          </div>

          <div><label for="education">Education:</label></div>
          <div><input type="text" id="education" name="education" value="<?php echo $edit_mode ? htmlspecialchars($edit_data['education']) : ''; ?>" placeholder="Your education qualification" /></div>

          <div><label for="presentAddress">Present Address:</label></div>
          <div><input type="text" id="presentAddress" name="presentAddress" value="<?php echo $edit_mode ? htmlspecialchars($edit_data['present_address']) : ''; ?>" placeholder="Current address" /></div>

          <div><label for="permanentAddress">Permanent Address:</label></div>
          <div><input type="text" id="permanentAddress" name="permanentAddress" value="<?php echo $edit_mode ? htmlspecialchars($edit_data['permanent_address']) : ''; ?>" placeholder="Permanent address" /></div>

          <div><label for="contactNumber">Contact Number:</label></div>
          <div><input type="tel" id="contactNumber" name="contactNumber" value="<?php echo $edit_mode ? htmlspecialchars($edit_data['contact_number']) : ''; ?>" placeholder="Phone number" /></div>

          <div><label for="email">Email ID:</label></div>
          <div><input type="email" id="email" name="email" value="<?php echo $edit_mode ? htmlspecialchars($edit_data['email']) : ''; ?>" placeholder="Email address" /></div>

          <div><label for="profile_photo">Upload Photo:</label></div>
          <div>
            <input type="file" id="profile_photo" name="profile_photo" accept="image/*" />
            <?php if ($edit_mode && $edit_data['profile_photo']): ?>
              <small style="color: #666;">Current: <?php echo basename($edit_data['profile_photo']); ?></small>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="section">
        <div class="section-title">Family Details</div>
        <div class="info-grid">
          <div><label for="fatherName">Father's Name & Occupation:</label></div>
          <div><input type="text" id="fatherName" name="fatherName" value="<?php echo $edit_mode ? htmlspecialchars($edit_data['father_name'] . ($edit_data['father_occupation'] ? ', ' . $edit_data['father_occupation'] : '')) : ''; ?>" placeholder="Name, Occupation" /></div>

          <div><label for="motherName">Mother's Name & Occupation:</label></div>
          <div><input type="text" id="motherName" name="motherName" value="<?php echo $edit_mode ? htmlspecialchars($edit_data['mother_name'] . ($edit_data['mother_occupation'] ? ', ' . $edit_data['mother_occupation'] : '')) : ''; ?>" placeholder="Name, Occupation" /></div>

          <div><label for="siblings">Siblings:</label></div>
          <div><input type="text" id="siblings" name="siblings" value="<?php echo $edit_mode ? htmlspecialchars($edit_data['siblings']) : ''; ?>" placeholder="Brothers and sisters information" /></div>

          <div>Family Type:</div>
          <div>
            <label><input type="radio" name="familyType" value="Joint" <?php echo ($edit_mode && $edit_data['family_type'] == 'Joint') ? 'checked' : ''; ?> /> Joint Family</label>
            <label><input type="radio" name="familyType" value="Nuclear" <?php echo ($edit_mode && $edit_data['family_type'] == 'Nuclear') ? 'checked' : ''; ?> /> Nuclear Family</label>
          </div>
        </div>
      </div>

      <div class="section">
        <div class="section-title">Hobbies & Interests</div>
        <?php
        $selected_hobbies = [];
        if ($edit_mode && $edit_data['hobbies']) {
            $selected_hobbies = array_map('trim', explode(',', $edit_data['hobbies']));
        }
        ?>
        <ul class="hobbies">
          <li><label><input type="checkbox" name="hobbies[]" value="Reading" <?php echo in_array('Reading', $selected_hobbies) ? 'checked' : ''; ?> /> Reading</label></li>
          <li><label><input type="checkbox" name="hobbies[]" value="Travelling" <?php echo in_array('Travelling', $selected_hobbies) ? 'checked' : ''; ?> /> Travelling</label></li>
          <li><label><input type="checkbox" name="hobbies[]" value="Spending time with family" <?php echo in_array('Spending time with family', $selected_hobbies) ? 'checked' : ''; ?> /> Spending time with family</label></li>
          <li><label><input type="checkbox" name="hobbies[]" value="Cooking" <?php echo in_array('Cooking', $selected_hobbies) ? 'checked' : ''; ?> /> Cooking</label></li>
          <li><label><input type="checkbox" name="hobbies[]" value="Gardening" <?php echo in_array('Gardening', $selected_hobbies) ? 'checked' : ''; ?> /> Gardening</label></li>
          <li><label><input type="checkbox" name="hobbies[]" value="Sports" <?php echo in_array('Sports', $selected_hobbies) ? 'checked' : ''; ?> /> Sports</label></li>
          <li><label><input type="checkbox" name="hobbies[]" value="Music" <?php echo in_array('Music', $selected_hobbies) ? 'checked' : ''; ?> /> Music</label></li>
          <li><label><input type="checkbox" name="hobbies[]" value="Movies" <?php echo in_array('Movies', $selected_hobbies) ? 'checked' : ''; ?> /> Movies</label></li>
        </ul>
      </div>

      <div class="submit-btn">
        <button type="submit"><?php echo $edit_mode ? '✏️ Update Biodata' : 'Submit Biodata'; ?></button>
      </div>
    </form>
  </div>
</body>
</html>
