<?php
// Include database connection
include 'connection.php';

// Start session
session_start();

// Function to fetch options from a table
function fetchOptions($table) {
    global $conn;
    $query = "SELECT name FROM `$table`";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        die('Error: ' . mysqli_error($conn));
    }
    $options = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $options[] = $row['name'];
    }
    return $options;
}

// Fetch options for branches, semesters, courses, divisions, and faculties
$branches = fetchOptions('branch');
$semesters = fetchOptions('semester');
$courses = fetchOptions('courses');
$divisions = fetchOptions('division');
$faculties = fetchOptions('faculty');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Report</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        header {
            background-color: #0073e6;
            color: white;
            padding: 5px 0;
            text-align: center;
        }
        .container {
            padding: 20px;
            margin-top: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .btn-primary {
            background-color: #0073e6;
            border: none;
        }
        .btn-primary:hover {
            background-color: #005bb5;
        }
    </style>
</head>
<body>
<header>
    <h1>Attendance Report</h1>
</header>

<div class="container">
    <h2>Generate Report</h2>
    <form action="generate_report.php" method="POST">
        <div class="form-group">
            <label for="branch">Branch:</label>
            <select id="branch" name="branch" class="form-control">
                <option value="">Select Branch</option>
                <?php foreach ($branches as $branch): ?>
                    <option value="<?= $branch ?>"><?= $branch ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="semester">Semester:</label>
            <select id="semester" name="semester" class="form-control">
                <option value="">Select Semester</option>
                <?php foreach ($semesters as $semester): ?>
                    <option value="<?= $semester ?>"><?= $semester ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="course">Course:</label>
            <select id="course" name="course" class="form-control">
                <option value="">Select Course</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?= $course ?>"><?= $course ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="division">Division:</label>
            <select id="division" name="division" class="form-control">
                <option value="">Select Division</option>
                <?php foreach ($divisions as $division): ?>
                    <option value="<?= $division ?>"><?= $division ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="faculty">Faculty:</label>
            <select id="faculty" name="faculty" class="form-control">
                <option value="">Select Faculty</option>
                <?php foreach ($faculties as $faculty): ?>
                    <option value="<?= $faculty ?>"><?= $faculty ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="date_from">From Date:</label>
            <input type="date" id="date_from" name="date_from" class="form-control">
        </div>

        <div class="form-group">
            <label for="date_to">To Date:</label>
            <input type="date" id="date_to" name="date_to" class="form-control">
        </div>

        <form action="generate_report.php" method="POST">
    <!-- Form fields -->
    <!-- Your existing form fields here -->
    <button type="submit" class="btn btn-primary">Generate Report</button>
</form>

    </form>
</div>

</body>
</html>
