<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "marriage_biodata_db";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get all biodata records
    $stmt = $pdo->query("SELECT * FROM biodata ORDER BY created_at DESC");
    $biodatas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Records - Marriage Biodata</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .back-btn {
            margin-bottom: 20px;
        }
        
        .back-btn a {
            background-color: #2c3e50;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 14px;
        }
        
        .back-btn a:hover {
            background-color: #405a75;
        }
        
        .record-count {
            background: #e8f4fd;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            color: #2c3e50;
            font-weight: bold;
        }
        
        .biodata-card {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            margin-bottom: 25px;
            padding: 20px;
            background: #fafafa;
            position: relative;
        }
        
        .biodata-header {
            background: #2c3e50;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .action-buttons {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 12px;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }
        
        .edit-btn {
            background-color: #ffc107;
            color: #212529;
        }
        
        .edit-btn:hover {
            background-color: #e0a800;
        }
        
        .delete-btn {
            background-color: #dc3545;
            color: white;
        }
        
        .delete-btn:hover {
            background-color: #c82333;
        }
        
        .profile-section {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .profile-photo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #2c3e50;
        }
        
        .basic-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            width: 150px;
            color: #495057;
        }
        
        .info-value {
            color: #212529;
        }
        
        .section-divider {
            border-top: 2px solid #dee2e6;
            margin: 20px 0;
            padding-top: 15px;
        }
        
        .section-title {
            background: #6c757d;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            font-size: 16px;
            margin-bottom: 10px;
            display: inline-block;
        }
        
        .no-records {
            text-align: center;
            padding: 50px;
            color: #6c757d;
            font-size: 18px;
        }
        
        .hobbies-list {
            background: #fff3cd;
            padding: 10px;
            border-radius: 5px;
            border-left: 4px solid #ffc107;
        }
    </style>
    <script>
        function confirmDelete(id, name) {
            if (confirm('Are you sure you want to delete the biodata of "' + name + '"? This action cannot be undone.')) {
                window.location.href = 'index.php?delete=' + id;
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="back-btn">
            <a href="index.php">← Back to Biodata Form</a>
        </div>
        
        <h1>📊 Database Records - Marriage Biodata</h1>
        
        <div class="record-count">
            Total Records in Database: <?php echo count($biodatas); ?>
        </div>
        
        <?php if (count($biodatas) > 0): ?>
            <?php foreach($biodatas as $index => $biodata): ?>
                <div class="biodata-card">
                    <div class="action-buttons">
                        <a href="index.php?edit=<?php echo $biodata['id']; ?>" class="btn edit-btn">✏️ Edit</a>
                        <button onclick="confirmDelete(<?php echo $biodata['id']; ?>, '<?php echo htmlspecialchars($biodata['full_name']); ?>')" class="btn delete-btn">🗑️ Delete</button>
                    </div>
                    
                    <div class="biodata-header">
                        <h3>Biodata #<?php echo $biodata['id']; ?> - <?php echo htmlspecialchars($biodata['full_name']); ?></h3>
                        <small>Added: <?php echo date('F j, Y - g:i A', strtotime($biodata['created_at'])); ?></small>
                    </div>
                    
                    <div class="profile-section">
                        <div>
                            <?php if($biodata['profile_photo'] && file_exists($biodata['profile_photo'])): ?>
                                <img src="<?php echo $biodata['profile_photo']; ?>" class="profile-photo" alt="Profile Photo">
                            <?php else: ?>
                                <div style="width: 120px; height: 120px; background: #dee2e6; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #6c757d;">No Photo</div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="basic-info">
                            <div>
                                <div class="info-row">
                                    <span class="info-label">Full Name:</span>
                                    <span class="info-value"><?php echo htmlspecialchars($biodata['full_name']); ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Date of Birth:</span>
                                    <span class="info-value"><?php echo date('F j, Y', strtotime($biodata['date_of_birth'])); ?> 
                                    (<?php echo date_diff(date_create($biodata['date_of_birth']), date_create('today'))->y; ?> years old)</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Gender:</span>
                                    <span class="info-value"><?php echo $biodata['gender']; ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Height:</span>
                                    <span class="info-value"><?php echo $biodata['height'] ?: 'Not specified'; ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Religion:</span>
                                    <span class="info-value"><?php echo $biodata['religion']; ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Nationality:</span>
                                    <span class="info-value"><?php echo $biodata['nationality']; ?></span>
                                </div>
                            </div>
                            
                            <div>
                                <div class="info-row">
                                    <span class="info-label">Marital Status:</span>
                                    <span class="info-value"><?php echo $biodata['marital_status']; ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Education:</span>
                                    <span class="info-value"><?php echo htmlspecialchars($biodata['education']) ?: 'Not specified'; ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Contact:</span>
                                    <span class="info-value"><?php echo $biodata['contact_number'] ?: 'Not provided'; ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Email:</span>
                                    <span class="info-value"><?php echo $biodata['email'] ?: 'Not provided'; ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Present Address:</span>
                                    <span class="info-value"><?php echo htmlspecialchars($biodata['present_address']) ?: 'Not specified'; ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Permanent Address:</span>
                                    <span class="info-value"><?php echo htmlspecialchars($biodata['permanent_address']) ?: 'Not specified'; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="section-divider">
                        <div class="section-title">👨‍👩‍👧‍👦 Family Details</div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div>
                                <div class="info-row">
                                    <span class="info-label">Father's Name:</span>
                                    <span class="info-value"><?php echo htmlspecialchars($biodata['father_name']) ?: 'Not specified'; ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Father's Occupation:</span>
                                    <span class="info-value"><?php echo htmlspecialchars($biodata['father_occupation']) ?: 'Not specified'; ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Mother's Name:</span>
                                    <span class="info-value"><?php echo htmlspecialchars($biodata['mother_name']) ?: 'Not specified'; ?></span>
                                </div>
                            </div>
                            <div>
                                <div class="info-row">
                                    <span class="info-label">Mother's Occupation:</span>
                                    <span class="info-value"><?php echo htmlspecialchars($biodata['mother_occupation']) ?: 'Not specified'; ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Siblings:</span>
                                    <span class="info-value"><?php echo htmlspecialchars($biodata['siblings']) ?: 'Not specified'; ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Family Type:</span>
                                    <span class="info-value"><?php echo $biodata['family_type'] ?: 'Not specified'; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if($biodata['hobbies']): ?>
                    <div class="section-divider">
                        <div class="section-title">🎯 Hobbies & Interests</div>
                        <div class="hobbies-list">
                            <?php echo htmlspecialchars($biodata['hobbies']); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-records">
                <h3>📝 No Records Found</h3>
                <p>No biodata has been submitted yet. <a href="index.php">Go back to submit the first biodata!</a></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
