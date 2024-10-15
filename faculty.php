<?php
    session_start();
    if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'faculty') {
        header("Location: login.php");
        exit();
    }

   

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <div class="header-content">
        <img src="logo.png" alt="College Logo" class="logo">
        <h1>Vidyalankar Institute of Technology</h1>
        <div class="header-buttons">
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                <button onclick="location.href='logout.php'">Logout</button>
            <?php else: ?>
                <button onclick="location.href='login.php'">Login</button>
            <?php endif; ?>
        </div>
    </div>

    <div class="navbar-container">
        <div class="navbar">
            <a href="index.php">Home</a>
            <a href="report.php">Report</a>
        </div>
    </div>
</header>

<main>
    <div class="container">
        <h2>Select Details</h2>
        <form id="" action="attendance.php" method="POST">
           

            <div class="dropdown-section">
                <label for="branch">Branch:</label>
                <select id="branch" name="branch" class="form-control" readonly>
                    <option value="">Select Branch</option>
                </select>
            </div>
            <br>
            <div class="dropdown-section">
                <label for="date">Select Date:</label>
                <input type="date" name="date" id="date" required>
            </div>

            <div class="dropdown-section">
                <label for="semester">Semester:</label>
                <select id="semester" name="semester" required>
                    <option value="">Select Semester</option>
                </select>
            </div>

            <div class="dropdown-section">
                <label for="course">Course:</label>
                <select id="course" name="course" required>
                    <option value="">Select Course</option>
                </select>
            </div>

            <div class="dropdown-section">
                <label for="division">Division:</label>
                <select id="division" name="division" required>
                    <option value="">Select Division</option>
                </select>
            </div>

            <div class="dropdown-section">
                <label for="TL">Theory/Lab:</label>
                <select id="TL" name="TL" required>
                    <option value="">T/L</option>
                    <option value="Theory">Theory</option>
                    <option value="Lab">Lab</option>
                </select>
            </div>

            <div class="dropdown-section">
                <label for="batch">Batch:</label>
                <select id="batch" name="batch" required>
                    <option value="">Select Batch</option>
                </select>
            </div>

            <div class="dropdown-section">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</main>


<script src="js/script.js"></script>



</body>
</html>
