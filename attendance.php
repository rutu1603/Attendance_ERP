<?php
include "connection.php";
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Fetch the student list from the database
$query = "SELECT * FROM students";
$result = $conn->query($query);
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
        .btn-default {
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
            color: white;
            background-color: black;
        }
        .btn-present {
            background-color: green;
        }
        .btn-absent {
            background-color: red;
        }
        .btn-marked {
            background-color: #6c757d;
        }
        .btn-marked-present {
            background-color: green;
        }
        .btn-marked-absent {
            background-color: red;
        }
    </style>
</head>
<body>
    <header>
        <!-- Include your header code here -->
    </header>

    <main>
        <div class="container">
            <h2>Attendance</h2>
            <form id="attendance-form" action="attendance_process.php" method="post">
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
                                    <button type="button" class="btn-default" onclick="markAttendance('<?php echo htmlspecialchars($row['roll_no']); ?>', 'present')">P</button>
                                    <button type="button" class="btn-default" onclick="markAttendance('<?php echo htmlspecialchars($row['roll_no']); ?>', 'absent')">A</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <input type="hidden" id="attendance_data" name="attendance_data">
                <button type="submit" class="btn btn-primary mt-3">Save Changes</button>
            </form>
        </div>
    </main>

    <script>
        function markAttendance(rollNo, status) {
            let dataField = document.getElementById('attendance_data');
            let currentData = dataField.value ? JSON.parse(dataField.value) : [];

            // Find existing entry or create a new one
            let entry = currentData.find(e => e.roll_no === rollNo);
            if (entry) {
                entry.status = status;
            } else {
                currentData.push({ roll_no: rollNo, status: status });
            }

            // Update button states
            document.querySelectorAll(`button[onclick*="${rollNo}"]`).forEach(button => {
                if (button.textContent === (status === 'present' ? 'P' : 'A')) {
                    button.classList.add(status === 'present' ? 'btn-marked-present' : 'btn-marked-absent');
                    button.classList.remove('btn-default');
                } else {
                    button.classList.add('btn-default');
                    button.classList.remove(status === 'present' ? 'btn-marked-present' : 'btn-marked-absent');
                }
            });

            // Update hidden field
            dataField.value = JSON.stringify(currentData);
        }
    </script>
</body>
</html>
