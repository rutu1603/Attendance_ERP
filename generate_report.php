<?php
// Include database connection
include 'connection.php';

// Start session
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Retrieve POST data
$branch = isset($_POST['branch']) ? $_POST['branch'] : '';
$semester = isset($_POST['semester']) ? $_POST['semester'] : '';
$course = isset($_POST['course']) ? $_POST['course'] : '';
$division = isset($_POST['division']) ? $_POST['division'] : '';
$date_from = isset($_POST['date_from']) ? $_POST['date_from'] : '';
$date_to = isset($_POST['date_to']) ? $_POST['date_to'] : '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Report</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" 
    integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" 
    crossorigin="anonymous">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Attendance Report</h2>
    
    <div class="card">
        <div class="card-body">
            <!-- Display selected filters -->
            <h5>Selected Filters:</h5>
            <p><strong>Branch:</strong> <?php echo htmlspecialchars($branch); ?></p>
            <p><strong>Semester:</strong> <?php echo htmlspecialchars($semester); ?></p>
            <p><strong>Course:</strong> <?php echo htmlspecialchars($course); ?></p>
            <p><strong>Division:</strong> <?php echo htmlspecialchars($division); ?></p>
            <p><strong>Date Range:</strong> <?php echo htmlspecialchars($date_from) . ' to ' . htmlspecialchars($date_to); ?></p>

            <!-- Attendance Table -->
            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                // Query to fetch student attendance within the selected range
                $query = "
                    SELECT a.roll_no, s.name,
                        SUM(CASE WHEN a.status = 1 THEN 1 ELSE 0 END) AS present_days,
                        COUNT(a.date) AS total_days
                    FROM attendance a
                    JOIN students s ON a.roll_no = s.roll_no
                    WHERE s.branch_name = ?
                    AND s.semester_name = ?
                    AND s.course_name = ?
                    AND s.division_name = ?
                    AND a.date BETWEEN ? AND ?
                    GROUP BY a.roll_no, s.name
                    ORDER BY a.roll_no ASC;
                ";

                // Prepare statement
                if ($stmt = $conn->prepare($query)) {
                    // Bind parameters
                    $stmt->bind_param("ssssss", $branch, $semester, $course, $division, $date_from, $date_to);

                    // Execute the query
                    $stmt->execute();

                    // Get the result
                    $result = $stmt->get_result();

                    // Fetch and display the data in a table format
                    if ($result->num_rows > 0) {
                        echo "<table class='table table-bordered mt-4'>";
                        echo "<thead class='table-dark'>";
                        echo "<tr>";
                        echo "<th>Serial No.</th>";
                        echo "<th>Roll No</th>";
                        echo "<th>Name</th>";
                        echo "<th>Present Days</th>";
                        echo "<th>Total Days</th>";
                        echo "<th>Attendance Percentage</th>";
                        echo "</tr>";
                        echo "</thead>";
                        echo "<tbody>";

                        $serial_no = 1;
                        while ($row = $result->fetch_assoc()) {
                            $present_days = $row['present_days'];
                            $total_days = $row['total_days'];
                            $percentage = ($total_days > 0) ? round(($present_days / $total_days) * 100, 2) : 0;

                            echo "<tr>";
                            echo "<td>" . $serial_no++ . "</td>";
                            echo "<td>" . htmlspecialchars($row['roll_no']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                            echo "<td>" . htmlspecialchars($present_days) . "</td>";
                            echo "<td>" . htmlspecialchars($total_days) . "</td>";
                            echo "<td>" . htmlspecialchars($percentage) . "%</td>";
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
        </div>
    </div>

    <!-- Button to download report -->
    <div class="mt-4">
        <a href="download_report.php?branch=<?php echo $branch; ?>&semester=<?php echo $semester; ?>&course=<?php echo $course; ?>&division=<?php echo $division; ?>&date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>" class="btn btn-primary">Download Report</a>
    </div>
</div>
</body>
</html>
