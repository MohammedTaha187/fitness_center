<?php

session_start();

if (!isset($_SESSION['user_id'])) {
  $_SESSION['error'] = "Please login to access this page.";
  header("Location: login.php");
  exit;
}

$host = "localhost";
$username = "root";
$password = "";
$database = "Fitness_Center";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

function sanitize($conn, $data)
{
  return $conn->real_escape_string(trim($data));
}

$successMessage = "";
$errorMessage = "";

$instructorId = "";
$instructorFullName = "";
$instructorEmail = "";
$instructorSalary = "";
$isEditingInstructor = false;

$memberId = "";
$memberFullName = "";
$memberAddress = "";
$isEditingMember = false;

$scheduleId = "";
$scheduleLocation = "";
$scheduleStartTime = "";
$scheduleEndTime = "";
$isEditingSchedule = false;

$classId = "";
$classInstructorId = "";
$classScheduleId = "";
$isEditingClass = false;

$planId = "";
$planPrice = "";
$planName = "";
$planBenefits = "";
$isEditingPlan = false;

$assessmentId = "";
$assessmentMemberId = "";
$assessmentDate = "";
$assessmentWeight = "";
$isEditingAssessment = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST['add_instructor']) || isset($_POST['update_instructor'])) {
    $instructorId = sanitize($conn, $_POST['instructor_id']);
    $instructorFullName = sanitize($conn, $_POST['instructor_full_name']);
    $instructorEmail = sanitize($conn, $_POST['instructor_email']);
    $instructorSalary = sanitize($conn, $_POST['instructor_salary']);

    if (isset($_POST['add_instructor'])) {
      $checkSql = "SELECT instructor_id FROM Instructors WHERE instructor_id='$instructorId'";
      $checkResult = $conn->query($checkSql);

      if ($checkResult && $checkResult->num_rows > 0) {
        $errorMessage = "Error: Instructor ID '$instructorId' already exists. Please use a different ID.";
      } else {
        $sql = "INSERT INTO Instructors (instructor_id, full_name, email, salary) 
                        VALUES ('$instructorId', '$instructorFullName', '$instructorEmail', '$instructorSalary')";

        if ($conn->query($sql) === TRUE) {
          $successMessage = "Instructor added successfully!";
          $instructorId = $instructorFullName = $instructorEmail = $instructorSalary = "";
        } else {
          $errorMessage = "Error: " . $conn->error;
        }
      }
    } elseif (isset($_POST['update_instructor'])) {
      $sql = "UPDATE Instructors SET full_name='$instructorFullName', email='$instructorEmail', salary='$instructorSalary' 
                    WHERE instructor_id='$instructorId'";

      if ($conn->query($sql) === TRUE) {
        $successMessage = "Instructor updated successfully!";
        $isEditingInstructor = false;
        $instructorId = $instructorFullName = $instructorEmail = $instructorSalary = "";
      } else {
        $errorMessage = "Error updating: " . $conn->error;
      }
    }
  }

  if (isset($_POST['add_member']) || isset($_POST['update_member'])) {
    $memberId = sanitize($conn, $_POST['member_id']);
    $memberFullName = sanitize($conn, $_POST['member_full_name']);
    $memberAddress = sanitize($conn, $_POST['member_address']);

    if (isset($_POST['add_member'])) {
      $checkSql = "SELECT member_id FROM Members WHERE member_id='$memberId'";
      $checkResult = $conn->query($checkSql);

      if ($checkResult && $checkResult->num_rows > 0) {
        $errorMessage = "Error: Member ID '$memberId' already exists. Please use a different ID.";
      } else {
        $sql = "INSERT INTO Members (member_id, full_name, address) 
                        VALUES ('$memberId', '$memberFullName', '$memberAddress')";

        if ($conn->query($sql) === TRUE) {
          $successMessage = "Member added successfully!";
          $memberId = $memberFullName = $memberAddress = "";
        } else {
          $errorMessage = "Error: " . $conn->error;
        }
      }
    } elseif (isset($_POST['update_member'])) {
      $sql = "UPDATE Members SET full_name='$memberFullName', address='$memberAddress' 
                    WHERE member_id='$memberId'";

      if ($conn->query($sql) === TRUE) {
        $successMessage = "Member updated successfully!";
        $isEditingMember = false;
        $memberId = $memberFullName = $memberAddress = "";
      } else {
        $errorMessage = "Error updating: " . $conn->error;
      }
    }
  }

  if (isset($_POST['add_schedule']) || isset($_POST['update_schedule'])) {
    $scheduleId = sanitize($conn, $_POST['schedule_id']);
    $scheduleLocation = sanitize($conn, $_POST['schedule_location']);
    $scheduleStartTime = sanitize($conn, $_POST['schedule_start_time']);
    $scheduleEndTime = sanitize($conn, $_POST['schedule_end_time']);

    if (isset($_POST['add_schedule'])) {
      $checkSql = "SELECT schedule_id FROM Schedules WHERE schedule_id='$scheduleId'";
      $checkResult = $conn->query($checkSql);

      if ($checkResult && $checkResult->num_rows > 0) {
        $errorMessage = "Error: Schedule ID '$scheduleId' already exists. Please use a different ID.";
      } else {
        $sql = "INSERT INTO Schedules (schedule_id, location, start_time, end_time) 
                        VALUES ('$scheduleId', '$scheduleLocation', '$scheduleStartTime', '$scheduleEndTime')";

        if ($conn->query($sql) === TRUE) {
          $successMessage = "Schedule added successfully!";
          $scheduleId = $scheduleLocation = $scheduleStartTime = $scheduleEndTime = "";
        } else {
          $errorMessage = "Error: " . $conn->error;
        }
      }
    } elseif (isset($_POST['update_schedule'])) {
      $sql = "UPDATE Schedules SET location='$scheduleLocation', start_time='$scheduleStartTime', end_time='$scheduleEndTime' 
                    WHERE schedule_id='$scheduleId'";

      if ($conn->query($sql) === TRUE) {
        $successMessage = "Schedule updated successfully!";
        $isEditingSchedule = false;
        $scheduleId = $scheduleLocation = $scheduleStartTime = $scheduleEndTime = "";
      } else {
        $errorMessage = "Error updating: " . $conn->error;
      }
    }
  }

  // Classes Form Processing
  if (isset($_POST['add_class']) || isset($_POST['update_class'])) {
    $classId = sanitize($conn, $_POST['class_id']);
    $classInstructorId = sanitize($conn, $_POST['class_instructor_id']);
    $classScheduleId = sanitize($conn, $_POST['class_schedule_id']);

    if (isset($_POST['add_class'])) {
      $checkSql = "SELECT class_id FROM Classes WHERE class_id='$classId'";
      $checkResult = $conn->query($checkSql);

      if ($checkResult && $checkResult->num_rows > 0) {
        $errorMessage = "Error: Class ID '$classId' already exists. Please use a different ID.";
      } else {
        $sql = "INSERT INTO Classes (class_id, instructor_id, schedule_id) 
                        VALUES ('$classId', '$classInstructorId', '$classScheduleId')";

        if ($conn->query($sql) === TRUE) {
          $successMessage = "Class added successfully!";
          $classId = $classInstructorId = $classScheduleId = "";
        } else {
          $errorMessage = "Error: " . $conn->error;
        }
      }
    } elseif (isset($_POST['update_class'])) {
      $sql = "UPDATE Classes SET instructor_id='$classInstructorId', schedule_id='$classScheduleId' 
                    WHERE class_id='$classId'";

      if ($conn->query($sql) === TRUE) {
        $successMessage = "Class updated successfully!";
        $isEditingClass = false;
        $classId = $classInstructorId = $classScheduleId = "";
      } else {
        $errorMessage = "Error updating: " . $conn->error;
      }
    }
  }

  if (isset($_POST['add_plan']) || isset($_POST['update_plan'])) {
    $planId = isset($_POST['plan_id']) ? sanitize($conn, $_POST['plan_id']) : "";
    $planPrice = sanitize($conn, $_POST['plan_price']);
    $planName = sanitize($conn, $_POST['plan_name']);
    $planBenefits = sanitize($conn, $_POST['plan_benefits']);

    if (isset($_POST['add_plan'])) {
      $checkSql = "SELECT plan_id FROM Plans WHERE plan_id='$planId'";
      $checkResult = $conn->query($checkSql);

      if ($checkResult && $checkResult->num_rows > 0) {
        $errorMessage = "Error: Plan ID '$planId' already exists. Please use a different ID.";
      } else {
        $sql = "INSERT INTO Plans (plan_id, price, plan_name, benefits) 
                        VALUES ('$planId', '$planPrice', '$planName', '$planBenefits')";

        if ($conn->query($sql) === TRUE) {
          $successMessage = "Plan added successfully!";
          $planId = $planPrice = $planName = $planBenefits = "";
        } else {
          $errorMessage = "Error: " . $conn->error;
        }
      }
    } elseif (isset($_POST['update_plan'])) {
      $sql = "UPDATE Plans SET price='$planPrice', plan_name='$planName', benefits='$planBenefits' 
                    WHERE plan_id='$planId'";

      if ($conn->query($sql) === TRUE) {
        $successMessage = "Plan updated successfully!";
        $isEditingPlan = false;
        $planId = $planPrice = $planName = $planBenefits = "";
      } else {
        $errorMessage = "Error updating: " . $conn->error;
      }
    }
  }

  if (isset($_POST['add_assessment']) || isset($_POST['update_assessment'])) {
    $assessmentId = sanitize($conn, $_POST['assessment_id']);
    $assessmentMemberId = sanitize($conn, $_POST['assessment_member_id']);
    $assessmentDate = sanitize($conn, $_POST['assessment_date']);
    $assessmentWeight = sanitize($conn, $_POST['assessment_weight']);

    if (isset($_POST['add_assessment'])) {
      $checkSql = "SELECT assessment_id FROM Assessments WHERE assessment_id='$assessmentId'";
      $checkResult = $conn->query($checkSql);

      if ($checkResult && $checkResult->num_rows > 0) {
        $errorMessage = "Error: Assessment ID '$assessmentId' already exists. Please use a different ID.";
      } else {
        $sql = "INSERT INTO Assessments (assessment_id, member_id, date, weight) 
                        VALUES ('$assessmentId', '$assessmentMemberId', '$assessmentDate', '$assessmentWeight')";

        if ($conn->query($sql) === TRUE) {
          $successMessage = "Assessment added successfully!";
          $assessmentId = $assessmentMemberId = $assessmentDate = $assessmentWeight = "";
        } else {
          $errorMessage = "Error: " . $conn->error;
        }
      }
    } elseif (isset($_POST['update_assessment'])) {
      $sql = "UPDATE Assessments SET member_id='$assessmentMemberId', date='$assessmentDate', weight='$assessmentWeight' 
                    WHERE assessment_id='$assessmentId'";

      if ($conn->query($sql) === TRUE) {
        $successMessage = "Assessment updated successfully!";
        $isEditingAssessment = false;
        $assessmentId = $assessmentMemberId = $assessmentDate = $assessmentWeight = "";
      } else {
        $errorMessage = "Error updating: " . $conn->error;
      }
    }
  }
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
  // Instructors
  if (isset($_GET['edit_instructor'])) {
    $id = sanitize($conn, $_GET['edit_instructor']);
    $sql = "SELECT * FROM Instructors WHERE instructor_id='$id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $instructorId = $row['instructor_id'];
      $instructorFullName = $row['full_name'];
      $instructorEmail = $row['email'];
      $instructorSalary = $row['salary'];
      $isEditingInstructor = true;
    }
  }

  if (isset($_GET['delete_instructor'])) {
    $id = sanitize($conn, $_GET['delete_instructor']);
    $sql = "DELETE FROM Instructors WHERE instructor_id='$id'";

    if ($conn->query($sql) === TRUE) {
      $successMessage = "Instructor deleted successfully!";
    } else {
      $errorMessage = "Error deleting: " . $conn->error;
    }
  }

  if (isset($_GET['edit_member'])) {
    $id = sanitize($conn, $_GET['edit_member']);
    $sql = "SELECT * FROM Members WHERE member_id='$id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $memberId = $row['member_id'];
      $memberFullName = $row['full_name'];
      $memberAddress = $row['address'];
      $isEditingMember = true;
    }
  }

  if (isset($_GET['delete_member'])) {
    $id = sanitize($conn, $_GET['delete_member']);
    $sql = "DELETE FROM Members WHERE member_id='$id'";

    if ($conn->query($sql) === TRUE) {
      $successMessage = "Member deleted successfully!";
    } else {
      $errorMessage = "Error deleting: " . $conn->error;
    }
  }

  if (isset($_GET['edit_schedule'])) {
    $id = sanitize($conn, $_GET['edit_schedule']);
    $sql = "SELECT * FROM Schedules WHERE schedule_id='$id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $scheduleId = $row['schedule_id'];
      $scheduleLocation = $row['location'];
      $scheduleStartTime = str_replace(' ', 'T', substr($row['start_time'], 0, 19));
      $scheduleEndTime = str_replace(' ', 'T', substr($row['end_time'], 0, 19));
      $isEditingSchedule = true;
    }
  }

  if (isset($_GET['delete_schedule'])) {
    $id = sanitize($conn, $_GET['delete_schedule']);
    $sql = "DELETE FROM Schedules WHERE schedule_id='$id'";

    if ($conn->query($sql) === TRUE) {
      $successMessage = "Schedule deleted successfully!";
    } else {
      $errorMessage = "Error deleting: " . $conn->error;
    }
  }

  if (isset($_GET['edit_class'])) {
    $id = sanitize($conn, $_GET['edit_class']);
    $sql = "SELECT * FROM Classes WHERE class_id='$id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $classId = $row['class_id'];
      $classInstructorId = $row['instructor_id'];
      $classScheduleId = $row['schedule_id'];
      $isEditingClass = true;
    }
  }

  if (isset($_GET['delete_class'])) {
    $id = sanitize($conn, $_GET['delete_class']);
    $sql = "DELETE FROM Classes WHERE class_id='$id'";

    if ($conn->query($sql) === TRUE) {
      $successMessage = "Class deleted successfully!";
    } else {
      $errorMessage = "Error deleting: " . $conn->error;
    }
  }

  if (isset($_GET['edit_plan'])) {
    $id = sanitize($conn, $_GET['edit_plan']);
    $sql = "SELECT * FROM Plans WHERE plan_id='$id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $planId = $row['plan_id'];
      $planPrice = $row['price'];
      $planName = $row['plan_name'];
      $planBenefits = $row['benefits'];
      $isEditingPlan = true;
    }
  }

  if (isset($_GET['delete_plan'])) {
    $id = sanitize($conn, $_GET['delete_plan']);
    $sql = "DELETE FROM Plans WHERE plan_id='$id'";

    if ($conn->query($sql) === TRUE) {
      $successMessage = "Plan deleted successfully!";
    } else {
      $errorMessage = "Error deleting: " . $conn->error;
    }
  }

  if (isset($_GET['edit_assessment'])) {
    $id = sanitize($conn, $_GET['edit_assessment']);
    $sql = "SELECT * FROM Assessments WHERE assessment_id='$id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $assessmentId = $row['assessment_id'];
      $assessmentMemberId = $row['member_id'];
      $assessmentDate = $row['date'];
      $assessmentWeight = $row['weight'];
      $isEditingAssessment = true;
    }
  }

  if (isset($_GET['delete_assessment'])) {
    $id = sanitize($conn, $_GET['delete_assessment']);
    $sql = "DELETE FROM Assessments WHERE assessment_id='$id'";

    if ($conn->query($sql) === TRUE) {
      $successMessage = "Assessment deleted successfully!";
    } else {
      $errorMessage = "Error deleting: " . $conn->error;
    }
  }
}

function getInstructors($conn)
{
  $sql = "SELECT * FROM Instructors";
  return $conn->query($sql);
}

function getMembers($conn)
{
  $sql = "SELECT * FROM Members";
  return $conn->query($sql);
}

function getSchedules($conn)
{
  $sql = "SELECT * FROM Schedules";
  return $conn->query($sql);
}

function getClasses($conn)
{
  $sql = "SELECT c.class_id, i.full_name AS instructor_name, 
           CONCAT(s.location, ' (', s.start_time, ')') AS schedule_info
           FROM Classes c
           LEFT JOIN Instructors i ON c.instructor_id = i.instructor_id
           LEFT JOIN Schedules s ON c.schedule_id = s.schedule_id";
  return $conn->query($sql);
}

function getPlans($conn)
{
  $sql = "SELECT * FROM Plans";
  return $conn->query($sql);
}

function getAssessments($conn)
{
  $sql = "SELECT a.assessment_id, a.date, a.weight, m.full_name AS member_name
            FROM Assessments a
            LEFT JOIN Members m ON a.member_id = m.member_id";
  return $conn->query($sql);
}

function getMemberOptions($conn)
{
  $sql = "SELECT member_id, full_name FROM Members";
  return $conn->query($sql);
}

function getInstructorOptions($conn)
{
  $sql = "SELECT instructor_id, full_name FROM Instructors";
  return $conn->query($sql);
}

function getScheduleOptions($conn)
{
  $sql = "SELECT schedule_id, CONCAT(location, ' (', start_time, ')') AS schedule_info FROM Schedules";
  return $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Fitness Center Management</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #4a90e2;
      --primary-dark: #3a7bc8;
      --success: #4CAF50;
      --error: #F44336;
      --bg: #f9f9fb;
      --card-bg: #fff;
      --text: #333;
      --text-light: #666;
      --border: #e0e0e0;
      --radius: 12px;
      --sidebar-bg: #2c3e50;
      --sidebar-text: #ecf0f1;
      --hover: #34495e;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--text);
      display: flex;
      min-height: 100vh;
      position: relative;
    }

    .sidebar {
      background: var(--sidebar-bg);
      width: 250px;
      padding: 30px 20px;
      display: flex;
      flex-direction: column;
      gap: 20px;
      transition: transform 0.3s ease;
      position: sticky;
      top: 0;
      height: 100vh;
      overflow-y: auto;
    }

    .sidebar h2 {
      color: var(--sidebar-text);
      margin-bottom: 20px;
    }

    .sidebar a {
      color: var(--sidebar-text);
      text-decoration: none;
      padding: 10px 15px;
      border-radius: 8px;
      transition: background 0.2s;
    }

    .sidebar a:hover {
      background: var(--hover);
    }

    .content {
      flex: 1;
      padding: 40px;
      overflow-y: auto;
    }

    section {
      background: var(--card-bg);
      margin-bottom: 40px;
      padding: 30px;
      border-radius: var(--radius);
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      animation: fadeIn 0.5s ease;
    }

    h1 {
      margin-bottom: 30px;
      color: var(--primary);
    }

    h2 {
      margin-bottom: 20px;
      color: var(--primary);
      border-left: 5px solid var(--primary);
      padding-left: 10px;
    }

    h3 {
      margin-bottom: 15px;
      color: #444;
    }

    form label {
      display: block;
      margin-top: 15px;
      font-weight: 600;
    }

    form input,
    form textarea,
    form select,
    form button {
      width: 100%;
      padding: 12px;
      margin-top: 5px;
      border-radius: 8px;
      border: 1px solid var(--border);
      margin-bottom: 15px;
      font-family: 'Inter', sans-serif;
      font-size: 15px;
    }

    form input:focus,
    form textarea:focus,
    form select:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.2);
    }

    form button {
      background: var(--primary);
      color: white;
      font-weight: 600;
      border: none;
      cursor: pointer;
      margin-top: 20px;
      transition: all 0.2s ease;
    }

    form button:hover {
      background: var(--primary-dark);
      transform: translateY(-1px);
    }

    form button+button {
      margin-top: 10px;
      background: #f5f5f5;
      color: var(--text);
      border: 1px solid var(--border);
    }

    form button+button:hover {
      background: #e5e5e5;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    table thead {
      background: var(--primary);
      color: white;
    }

    table th,
    table td {
      padding: 12px;
      border: 1px solid var(--border);
      text-align: left;
    }

    table tr:nth-child(even) {
      background-color: #f8f9fa;
    }

    .action-links a {
      display: inline-block;
      margin-right: 10px;
      text-decoration: none;
      color: var(--primary);
      font-weight: 600;
    }

    .action-links a:hover {
      text-decoration: underline;
    }

    .notification-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 1000;
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.3s, visibility 0.3s;
    }

    .notification-overlay.show {
      opacity: 1;
      visibility: visible;
    }

    .notification {
      max-width: 500px;
      width: 90%;
      background: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
      transform: translateY(-20px);
      transition: transform 0.3s;
    }

    .notification-overlay.show .notification {
      transform: translateY(0);
    }

    .notification.success {
      border-top: 5px solid var(--success);
    }

    .notification.error {
      border-top: 5px solid var(--error);
    }

    .notification h3 {
      margin-top: 0;
      font-size: 18px;
      font-weight: 600;
    }

    .notification-success h3 {
      color: var(--success);
    }

    .notification-error h3 {
      color: var(--error);
    }

    .notification p {
      margin: 10px 0 20px;
      color: var(--text-light);
    }

    .notification-btn {
      background: var(--primary);
      color: white;
      border: none;
      padding: 10px 15px;
      border-radius: 5px;
      cursor: pointer;
      float: right;
      font-weight: 500;
      transition: background 0.2s;
    }

    .notification-btn:hover {
      background: var(--primary-dark);
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @media (max-width: 768px) {
      body {
        flex-direction: column;
      }

      .sidebar {
        width: 100%;
        height: auto;
        position: relative;
      }

      .content {
        padding: 20px;
      }

      table {
        display: block;
        overflow-x: auto;
      }
    }
  </style>
</head>

<body>
  <div class="sidebar">
    <h2>Dashboard</h2>
    <a href="#instructors">Instructors</a>
    <a href="#members">Members</a>
    <a href="#schedules">Schedules</a>
    <a href="#classes">Classes</a>
    <a href="#plans">Plans</a>
    <a href="#assessments">Assessments</a>

    <hr />
    <a href="logout.php" style="color: red;">Logout</a>
  </div>


  <div class="content">
    <h1>Fitness Center Management</h1>

    <section id="instructors">
      <h2>Instructors Management</h2>
      <form method="POST" action="#instructors">
        <input type="hidden" name="<?php echo $isEditingInstructor ? 'update_instructor' : 'add_instructor'; ?>" value="1">
        <label for="instructor_id">Instructor ID</label>
        <input type="text" id="instructor_id" name="instructor_id" value="<?php echo $instructorId; ?>" <?php echo $isEditingInstructor ? 'readonly' : ''; ?> required>
        <label for="instructor_full_name">Full Name</label>
        <input type="text" id="instructor_full_name" name="instructor_full_name" value="<?php echo $instructorFullName; ?>" required>
        <label for="instructor_email">Email</label>
        <input type="email" id="instructor_email" name="instructor_email" value="<?php echo $instructorEmail; ?>" required>
        <label for="instructor_salary">Salary</label>
        <input type="number" id="instructor_salary" name="instructor_salary" value="<?php echo $instructorSalary; ?>" required>
        <button type="submit"><?php echo $isEditingInstructor ? 'Update Instructor' : 'Add Instructor'; ?></button>
        <?php if ($isEditingInstructor): ?>
          <button type="button" onclick="window.location.href='#instructors'">Cancel</button>
        <?php endif; ?>
      </form>

      <h3>Data</h3>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Salary</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $result = getInstructors($conn);
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . $row["instructor_id"] . "</td>";
              echo "<td>" . $row["full_name"] . "</td>";
              echo "<td>" . $row["email"] . "</td>";
              echo "<td>" . $row["salary"] . "</td>";
              echo "<td class='action-links'>";
              echo "<a href='?edit_instructor=" . $row["instructor_id"] . "#instructors'>Edit</a>";
              echo "<a href='?delete_instructor=" . $row["instructor_id"] . "#instructors' onclick='return confirm(\"Are you sure?\")'>Delete</a>";
              echo "</td>";
              echo "</tr>";
            }
          } else {
            echo "<tr><td colspan='5'>No instructors found</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </section>

    <section id="members">
      <h2>Members Management</h2>
      <form method="POST" action="#members">
        <input type="hidden" name="<?php echo $isEditingMember ? 'update_member' : 'add_member'; ?>" value="1">
        <label for="member_id">Member ID</label>
        <input type="text" id="member_id" name="member_id" value="<?php echo $memberId; ?>" <?php echo $isEditingMember ? 'readonly' : ''; ?> required>
        <label for="member_full_name">Full Name</label>
        <input type="text" id="member_full_name" name="member_full_name" value="<?php echo $memberFullName; ?>" required>
        <label for="member_address">Address</label>
        <input type="text" id="member_address" name="member_address" value="<?php echo $memberAddress; ?>" required>
        <button type="submit"><?php echo $isEditingMember ? 'Update Member' : 'Add Member'; ?></button>
        <?php if ($isEditingMember): ?>
          <button type="button" onclick="window.location.href='#members'">Cancel</button>
        <?php endif; ?>
      </form>

      <h3>Data</h3>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Address</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $result = getMembers($conn);
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . $row["member_id"] . "</td>";
              echo "<td>" . $row["full_name"] . "</td>";
              echo "<td>" . $row["address"] . "</td>";
              echo "<td class='action-links'>";
              echo "<a href='?edit_member=" . $row["member_id"] . "#members'>Edit</a>";
              echo "<a href='?delete_member=" . $row["member_id"] . "#members' onclick='return confirm(\"Are you sure?\")'>Delete</a>";
              echo "</td>";
              echo "</tr>";
            }
          } else {
            echo "<tr><td colspan='4'>No members found</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </section>

    <section id="schedules">
      <h2>Schedules Management</h2>
      <form method="POST" action="#schedules">
        <input type="hidden" name="<?php echo $isEditingSchedule ? 'update_schedule' : 'add_schedule'; ?>" value="1">
        <label for="schedule_id">Schedule ID</label>
        <input type="text" id="schedule_id" name="schedule_id" value="<?php echo $scheduleId; ?>" <?php echo $isEditingSchedule ? 'readonly' : ''; ?> required>
        <label for="schedule_location">Location</label>
        <input type="text" id="schedule_location" name="schedule_location" value="<?php echo $scheduleLocation; ?>" required>
        <label for="schedule_start_time">Start Time</label>
        <input type="datetime-local" id="schedule_start_time" name="schedule_start_time" value="<?php echo $scheduleStartTime; ?>" required>
        <label for="schedule_end_time">End Time</label>
        <input type="datetime-local" id="schedule_end_time" name="schedule_end_time" value="<?php echo $scheduleEndTime; ?>" required>
        <button type="submit"><?php echo $isEditingSchedule ? 'Update Schedule' : 'Add Schedule'; ?></button>
        <?php if ($isEditingSchedule): ?>
          <button type="button" onclick="window.location.href='#schedules'">Cancel</button>
        <?php endif; ?>
      </form>

      <h3>Data</h3>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Location</th>
            <th>Start</th>
            <th>End</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $result = getSchedules($conn);
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . $row["schedule_id"] . "</td>";
              echo "<td>" . $row["location"] . "</td>";
              echo "<td>" . $row["start_time"] . "</td>";
              echo "<td>" . $row["end_time"] . "</td>";
              echo "<td class='action-links'>";
              echo "<a href='?edit_schedule=" . $row["schedule_id"] . "#schedules'>Edit</a>";
              echo "<a href='?delete_schedule=" . $row["schedule_id"] . "#schedules' onclick='return confirm(\"Are you sure?\")'>Delete</a>";
              echo "</td>";
              echo "</tr>";
            }
          } else {
            echo "<tr><td colspan='5'>No schedules found</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </section>

    <section id="classes">
      <h2>Classes Management</h2>
      <form method="POST" action="#classes">
        <input type="hidden" name="<?php echo $isEditingClass ? 'update_class' : 'add_class'; ?>" value="1">
        <label for="class_id">Class ID</label>
        <input type="text" id="class_id" name="class_id" value="<?php echo $classId; ?>" <?php echo $isEditingClass ? 'readonly' : ''; ?> required>
        <label for="class_instructor_id">Instructor</label>
        <select id="class_instructor_id" name="class_instructor_id" required>
          <option value="">Select Instructor</option>
          <?php
          $instructors = getInstructorOptions($conn);
          while ($instructor = $instructors->fetch_assoc()) {
            $selected = ($instructor['instructor_id'] == $classInstructorId) ? 'selected' : '';
            echo "<option value='" . $instructor['instructor_id'] . "' $selected>" . $instructor['full_name'] . "</option>";
          }
          ?>
        </select>
        <label for="class_schedule_id">Schedule</label>
        <select id="class_schedule_id" name="class_schedule_id" required>
          <option value="">Select Schedule</option>
          <?php
          $schedules = getScheduleOptions($conn);
          while ($schedule = $schedules->fetch_assoc()) {
            $selected = ($schedule['schedule_id'] == $classScheduleId) ? 'selected' : '';
            echo "<option value='" . $schedule['schedule_id'] . "' $selected>" . $schedule['schedule_info'] . "</option>";
          }
          ?>
        </select>
        <button type="submit"><?php echo $isEditingClass ? 'Update Class' : 'Add Class'; ?></button>
        <?php if ($isEditingClass): ?>
          <button type="button" onclick="window.location.href='#classes'">Cancel</button>
        <?php endif; ?>
      </form>

      <h3>Data</h3>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Instructor</th>
            <th>Schedule</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $result = getClasses($conn);
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . $row["class_id"] . "</td>";
              echo "<td>" . $row["instructor_name"] . "</td>";
              echo "<td>" . $row["schedule_info"] . "</td>";
              echo "<td class='action-links'>";
              echo "<a href='?edit_class=" . $row["class_id"] . "#classes'>Edit</a>";
              echo "<a href='?delete_class=" . $row["class_id"] . "#classes' onclick='return confirm(\"Are you sure?\")'>Delete</a>";
              echo "</td>";
              echo "</tr>";
            }
          } else {
            echo "<tr><td colspan='4'>No classes found</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </section>

    <section id="plans">
      <h2>Membership Plans</h2>
      <form method="POST" action="#plans">
        <input type="hidden" name="<?php echo $isEditingPlan ? 'update_plan' : 'add_plan'; ?>" value="1">
        <?php if ($isEditingPlan): ?>
          <input type="hidden" name="plan_id" value="<?php echo $planId; ?>">
        <?php else: ?>
          <label for="plan_id">Plan ID</label>
          <input type="text" id="plan_id" name="plan_id" value="<?php echo $planId; ?>" required>
        <?php endif; ?>
        <label for="plan_price">Price</label>
        <input type="number" id="plan_price" name="plan_price" value="<?php echo $planPrice; ?>" step="0.01" required>
        <label for="plan_name">Plan Name</label>
        <input type="text" id="plan_name" name="plan_name" value="<?php echo $planName; ?>" required>
        <label for="plan_benefits">Benefits</label>
        <textarea id="plan_benefits" name="plan_benefits" rows="4"><?php echo $planBenefits; ?></textarea>
        <button type="submit"><?php echo $isEditingPlan ? 'Update Plan' : 'Add Plan'; ?></button>
        <?php if ($isEditingPlan): ?>
          <button type="button" onclick="window.location.href='#plans'">Cancel</button>
        <?php endif; ?>
      </form>

      <h3>Data</h3>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Price</th>
            <th>Name</th>
            <th>Benefits</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $result = getPlans($conn);
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . $row["plan_id"] . "</td>";
              echo "<td>" . $row["price"] . "</td>";
              echo "<td>" . $row["plan_name"] . "</td>";
              echo "<td>" . $row["benefits"] . "</td>";
              echo "<td class='action-links'>";
              echo "<a href='?edit_plan=" . $row["plan_id"] . "#plans'>Edit</a>";
              echo "<a href='?delete_plan=" . $row["plan_id"] . "#plans' onclick='return confirm(\"Are you sure?\")'>Delete</a>";
              echo "</td>";
              echo "</tr>";
            }
          } else {
            echo "<tr><td colspan='5'>No plans found</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </section>

    <section id="assessments">
      <h2>Health Assessments</h2>
      <form method="POST" action="#assessments">
        <input type="hidden" name="<?php echo $isEditingAssessment ? 'update_assessment' : 'add_assessment'; ?>" value="1">
        <label for="assessment_id">Assessment ID</label>
        <input type="text" id="assessment_id" name="assessment_id" value="<?php echo $assessmentId; ?>" <?php echo $isEditingAssessment ? 'readonly' : ''; ?> required>
        <label for="assessment_member_id">Member</label>
        <select id="assessment_member_id" name="assessment_member_id" required>
          <option value="">Select Member</option>
          <?php
          $members = getMemberOptions($conn);
          while ($member = $members->fetch_assoc()) {
            $selected = ($member['member_id'] == $assessmentMemberId) ? 'selected' : '';
            echo "<option value='" . $member['member_id'] . "' $selected>" . $member['full_name'] . "</option>";
          }
          ?>
        </select>
        <label for="assessment_date">Date</label>
        <input type="date" id="assessment_date" name="assessment_date" value="<?php echo $assessmentDate; ?>" required>
        <label for="assessment_weight">Weight (kg)</label>
        <input type="number" id="assessment_weight" name="assessment_weight" value="<?php echo $assessmentWeight; ?>" step="0.01" required>
        <button type="submit"><?php echo $isEditingAssessment ? 'Update Assessment' : 'Add Assessment'; ?></button>
        <?php if ($isEditingAssessment): ?>
          <button type="button" onclick="window.location.href='#assessments'">Cancel</button>
        <?php endif; ?>
      </form>

      <h3>Data</h3>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Member</th>
            <th>Date</th>
            <th>Weight</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $result = getAssessments($conn);
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . $row["assessment_id"] . "</td>";
              echo "<td>" . $row["member_name"] . "</td>";
              echo "<td>" . $row["date"] . "</td>";
              echo "<td>" . $row["weight"] . " kg</td>";
              echo "<td class='action-links'>";
              echo "<a href='?edit_assessment=" . $row["assessment_id"] . "#assessments'>Edit</a>";
              echo "<a href='?delete_assessment=" . $row["assessment_id"] . "#assessments' onclick='return confirm(\"Are you sure?\")'>Delete</a>";
              echo "</td>";
              echo "</tr>";
            }
          } else {
            echo "<tr><td colspan='5'>No assessments found</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </section>
  </div>

  <div id="notification-overlay" class="notification-overlay">
    <div id="notification" class="notification">
      <h3 id="notification-title">Notification</h3>
      <p id="notification-message"></p>
      <button class="notification-btn" onclick="closeNotification()">OK</button>
    </div>
  </div>

  <script>
    <?php if (!empty($successMessage)): ?>
      showNotification('Success', '<?php echo $successMessage; ?>', 'success');
    <?php endif; ?>

    <?php if (!empty($errorMessage)): ?>
      showNotification('Error', '<?php echo $errorMessage; ?>', 'error');
    <?php endif; ?>

    function showNotification(title, message, type) {
      document.getElementById('notification-title').innerText = title;
      document.getElementById('notification-message').innerText = message;

      const notification = document.getElementById('notification');
      notification.className = 'notification';
      notification.classList.add(type);

      const overlay = document.getElementById('notification-overlay');
      overlay.classList.add('show');
    }

    function closeNotification() {
      const overlay = document.getElementById('notification-overlay');
      overlay.classList.remove('show');
    }


    document.getElementById('notification-overlay').addEventListener('click', function(e) {
      if (e.target === this) {
        closeNotification();
      }
    });

    function highlightActiveLink() {
      const hash = window.location.hash || '#instructors';
      const links = document.querySelectorAll('.sidebar a');

      links.forEach(link => {
        if (link.getAttribute('href') === hash) {
          link.style.backgroundColor = 'var(--hover)';
        } else {
          link.style.backgroundColor = 'transparent';
        }
      });
    }

    highlightActiveLink();

    window.addEventListener('hashchange', highlightActiveLink);
  </script>
</body>

</html>