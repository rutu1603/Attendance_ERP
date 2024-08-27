<?php
// Include database connection
include 'connection.php';

// Start session
session_start();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $branch = $_POST['branch'];
    $semester = $_POST['semester'];
    $course = $_POST['course'];
    $division = $_POST['division'];
    $faculty = $_POST['faculty'];
    $date_from = $_POST['date_from'];
    $date_to = $_POST['date_to'];

    // Prepare query to fetch sorted student attendance data
    $query = "SELECT a.roll_no, s.name, a.date, a.status 
              FROM attendance a
              JOIN students s ON a.roll_no = s.roll_no
              WHERE s.branch_name = ? 
              AND s.semester_name = ? 
              AND s.course_name = ? 
              AND s.division_name = ? 
              AND s.faculty_name = ? 
              AND a.date BETWEEN ? AND ?
              ORDER BY a.roll_no ASC";

    // Prepare statement
    if ($stmt = $conn->prepare($query)) {
        // Bind parameters
        $stmt->bind_param("sssssss", $branch, $semester, $course, $division, $faculty, $date_from, $date_to);

        // Execute the query
        $stmt->execute();

        // Get the result
        $result = $stmt->get_result();

        // Fetch and display the data in a table format
        if ($result->num_rows > 0) {
            echo "<table class='table table-bordered'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>Roll No</th>";
            echo "<th>Name</th>";
            echo "<th>Date</th>";
            echo "<th>Status</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
            
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['roll_no']) . "</td>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                echo "</tr>";
            }
            
            echo "</tbody>";
            echo "</table>";
        } else {
            echo "<p>No attendance records found for the selected criteria.</p>";
        }

        // Close statement
        $stmt->close();
    } else {
        die('Query preparation failed: ' . $conn->error);
    }
}

// Close connection
$conn->close();
?>
