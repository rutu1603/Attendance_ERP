<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Include database connection
include 'connection.php';

// Start session
session_start();

// Get parameters from POST
$branch = $_POST['branch'];
$semester = $_POST['semester'];
$course = $_POST['course'];
$division = $_POST['division'];
$faculty = $_POST['faculty'];
$date_from = $_POST['date_from'];
$date_to = $_POST['date_to'];

// Prepare query to fetch student attendance data
$query = "SELECT s.roll_no, s.name, 
                 SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) AS total_days_present, 
                 COUNT(a.date) AS total_days,
                 (SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) / COUNT(a.date)) * 100 AS percentage
          FROM attendance a
          JOIN students s ON a.roll_no = s.roll_no
          WHERE s.branch_name = ? 
          AND s.semester_name = ? 
          AND s.course_name = ? 
          AND s.division_name = ? 
          AND s.faculty_name = ? 
          AND a.date BETWEEN ? AND ?
          GROUP BY s.roll_no, s.name
          ORDER BY s.roll_no ASC";

// Prepare statement
if ($stmt = $conn->prepare($query)) {
    // Bind parameters
    $stmt->bind_param("sssssss", $branch, $semester, $course, $division, $faculty, $date_from, $date_to);

    // Execute the query
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Create new Spreadsheet object
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set table headers
    $sheet->setCellValue('A1', 'Roll No');
    $sheet->setCellValue('B1', 'Name');
    $sheet->setCellValue('C1', 'Total Days Present');
    $sheet->setCellValue('D1', 'Total Days');
    $sheet->setCellValue('E1', 'Percentage');

    // Write data to cells
    $rowCount = 2;
    while ($row = $result->fetch_assoc()) {
        $sheet->setCellValue('A' . $rowCount, $row['roll_no']);
        $sheet->setCellValue('B' . $rowCount, $row['name']);
        $sheet->setCellValue('C' . $rowCount, $row['total_days_present']);
        $sheet->setCellValue('D' . $rowCount, $row['total_days']);
        $sheet->setCellValue('E' . $rowCount, number_format($row['percentage'], 2) . '%');
        $rowCount++;
    }

    // Create Writer object and save the file
    $writer = new Xlsx($spreadsheet);
    $filename = 'attendance_report_' . date('Ymd') . '.xlsx';
    
    // Send file to browser for download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    $writer->save('php://output');
    
    // Close statement
    $stmt->close();
} else {
    die('Query preparation failed: ' . $conn->error);
}

// Close connection
$conn->close();
?>
