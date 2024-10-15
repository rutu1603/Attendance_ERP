<?php
include 'connection.php';
session_start();

// Verify user access
if (!isset($_SESSION['logged_in']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'faculty' && $_SESSION['role'] !== 'executive')) {
    header("Location: login.php");
    exit();
}

// Fetch the form data with isset() check to prevent warnings
$branch = isset($_POST['branch']) ? $_POST['branch'] : '';
$semester = isset($_POST['semester']) ? $_POST['semester'] : '';
$course = isset($_POST['course']) ? $_POST['course'] : '';
$division = isset($_POST['division']) ? $_POST['division'] : '';
$from_date = isset($_POST['from_date']) ? $_POST['from_date'] : '';
$to_date = isset($_POST['to_date']) ? $_POST['to_date'] : '';

// Handle the case where form fields are not set (optional)
if (empty($branch) || empty($semester) || empty($course) || empty($division) || empty($from_date) || empty($to_date)) {
    echo "Please fill in all the required fields.";
    exit();
}

// Query for generating theory attendance report
$query = "SELECT a.roll_no, s.name,
    COUNT(DISTINCT CASE WHEN a.status = 1 THEN a.date END) AS present_days,  -- Count distinct present days
    COUNT(DISTINCT a.date) AS total_days,  -- Count distinct total days
    (COUNT(DISTINCT CASE WHEN a.status = 1 THEN a.date END) / COUNT(DISTINCT a.date)) * 100 AS percentage
FROM attendance a
JOIN students s ON a.roll_no = s.roll_no
JOIN student_course sc ON s.roll_no = sc.roll_no
WHERE s.branch_name = ? 
AND s.semester = ? 
AND sc.course = ? 
AND s.division_name = ? 
AND a.date BETWEEN ? AND ?
GROUP BY a.roll_no, s.name
ORDER BY a.roll_no ASC;";

// Prepare and bind the query
$stmt = $conn->prepare($query);
$stmt->bind_param("ssssss", $branch, $semester, $course, $division, $from_date, $to_date);
$stmt->execute();
$result = $stmt->get_result();

// Output the report (HTML table)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theory Attendance Report</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    
    <script>
        // Function to prevent form submission on download
        function downloadReport(event) {
            event.preventDefault();  // Prevent form from submitting and leaving the page
            window.print();  // Trigger print/download option
        }
    </script>
</head>
<body>
<div class="container">

 <!-- Back Button -->
 <button class="btn btn-primary" onclick="history.back()">Back</button>

    <h2>Theory Attendance Report</h2>

    <!-- Display selected data -->
    <div class="row">
        <div class="col-md-6">
            <p><strong>Branch:</strong> <?php echo htmlspecialchars($branch); ?></p>
            <p><strong>Semester:</strong> <?php echo htmlspecialchars($semester); ?></p>
            <p><strong>course:</strong> <?php echo htmlspecialchars($course); ?></p>
        </div>
        <div class="col-md-6">
            <p><strong>Division:</strong> <?php echo htmlspecialchars($division); ?></p>
            <p><strong>Date Range:</strong> <?php echo htmlspecialchars($from_date) . ' to ' . htmlspecialchars($to_date); ?></p>
        </div>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Roll No</th>
                <th>Name</th>
                <th>Present Days</th>
                <th>Total Days</th>
                <th>Percentage (%)</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['roll_no']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['present_days']); ?></td>
                    <td><?php echo htmlspecialchars($row['total_days']); ?></td>
                    <td><?php echo number_format($row['percentage'], 2); ?>%</td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="dropdown-section">
        <form action="generate_T_pdf.php" method="post">
            <input type="hidden" name="branch" value="<?php echo htmlspecialchars($branch); ?>">
            <input type="hidden" name="semester" value="<?php echo htmlspecialchars($semester); ?>">
            <input type="hidden" name="course" value="<?php echo htmlspecialchars($course); ?>">
            <input type="hidden" name="division" value="<?php echo htmlspecialchars($division); ?>">
            <input type="hidden" name="from_date" value="<?php echo htmlspecialchars($from_date); ?>">
            <input type="hidden" name="to_date" value="<?php echo htmlspecialchars($to_date); ?>">
            <button class="btn btn-primary"type="button" onclick="downloadReport(event)">Download Page as PDF</button>
        </form>
    </div>

</div>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
