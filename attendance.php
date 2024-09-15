<?php
include "connection.php";
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Retrieve selected filters from POST request
$selectedDate = isset($_POST['date']) ? $_POST['date'] : date('Y-m-d');
$branchName = isset($_POST['branch']) ? $_POST['branch'] : '';
$semesterName = isset($_POST['semester']) ? $_POST['semester'] : '';
$divisionName = isset($_POST['division']) ? $_POST['division'] : '';
$courseName = isset($_POST['course']) ? $_POST['course'] : '';
$facultyName = isset($_POST['faculty']) ? $_POST['faculty'] : '';

// Fetch filtered students based on the selected criteria
$query = "SELECT roll_no, name FROM students WHERE branch_name = ? AND semester_name = ? AND division_name = ? AND course_name = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssss", $branchName, $semesterName, $divisionName, $courseName);

if ($stmt->execute()) {
    $result = $stmt->get_result();
} else {
    // Handle query execution error
    echo "Error: " . $stmt->error;
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #0073e6;
            color: white;
        }
        .btn-present {
            background-color: green;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
        }
        .btn-absent {
            background-color: red;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
        }
        .info {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }
        .info-row p {
            margin: 0;
            padding: 5px;
            flex: 1;
        }
        .info-row p b {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Attendance</h2>

        <!-- Display selected filters -->
        <div class="info">
            <div class="info-row">
                <p><b>Date:</b> <?php echo htmlspecialchars($selectedDate); ?></p>
                <p><b>Branch:</b> <?php echo htmlspecialchars($branchName); ?></p>
                <p><b>Semester:</b> <?php echo htmlspecialchars($semesterName); ?></p>
                <p><b>Division:</b> <?php echo htmlspecialchars($divisionName); ?></p>
                <p><b>Course:</b> <?php echo htmlspecialchars($courseName); ?></p>
                <p><b>Faculty:</b> <?php echo htmlspecialchars($facultyName); ?></p>
            </div>
        </div>

        <form id="attendance-form" action="attendance_process.php" method="post">
    <!-- Hidden fields to retain filters -->
    <input type="hidden" name="selected_date" value="<?php echo htmlspecialchars($selectedDate); ?>">
    <input type="hidden" name="branch" value="<?php echo htmlspecialchars($branchName); ?>">
    <input type="hidden" name="semester" value="<?php echo htmlspecialchars($semesterName); ?>">
    <input type="hidden" name="division" value="<?php echo htmlspecialchars($divisionName); ?>">
    <input type="hidden" name="course" value="<?php echo htmlspecialchars($courseName); ?>">
    <input type="hidden" name="faculty" value="<?php echo htmlspecialchars($facultyName); ?>">

    <table>
        <thead>
            <tr>
                <th>Serial No</th>
                <th>Roll No</th>
                <th>Name</th>
                <th>Attendance</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo htmlspecialchars($row['roll_no']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td>
                        <!-- Default value is Present (1) -->
                        <input type="hidden" name="attendance[<?php echo htmlspecialchars($row['roll_no']); ?>][name]" value="<?php echo htmlspecialchars($row['name']); ?>">
                        <input type="hidden" name="attendance[<?php echo htmlspecialchars($row['roll_no']); ?>][status]" class="attendance-status" value="1">
                        <button type="button" class="btn-present" onclick="toggleAttendance('<?php echo htmlspecialchars($row['roll_no']); ?>', this)">1</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <button type="submit" class="btn btn-primary mt-3">Save Changes</button>
</form>


    </div>

    <script>
        // Function to toggle attendance between Present (1) and Absent (0)
function toggleAttendance(roll_no, button) {
    var statusField = document.querySelector('input[name="attendance[' + roll_no + '][status]"]');
    if (button.classList.contains('btn-present')) {
        // Switch to Absent (0)
        button.classList.remove('btn-present');
        button.classList.add('btn-absent');
        button.textContent = '0';  // Display 0 for Absent
        statusField.value = '0';  // Set status to 0 for Absent
    } else {
        // Switch back to Present (1)
        button.classList.remove('btn-absent');
        button.classList.add('btn-present');
        button.textContent = '1';  // Display 1 for Present
        statusField.value = '1';  // Set status to 1 for Present
    }
}

    </script>
</body>
</html>
