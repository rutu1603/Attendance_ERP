<?php include "connection.php"; ?>
<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Home Page'; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header {
            background-color: #0073e6;
            color: white;
            padding: 5px 0;
            text-align: center;
            position: relative;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
        }

        .logo {
            height: 40px;
        }

        .header-buttons button {
            background-color: #005bb5;
            color: white;
            border: none;
            padding: 5px 10px;
            margin-left: 10px;
            cursor: pointer;
            border-radius: 5px;
        }

        .header-buttons button:hover {
            background-color: #004494;
        }

        main {
            padding: 20px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
        }

        .selection-form {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .dropdown-section {
            display: inline;
            align-items: center;
            gap: 20px;
        }

        label {
            font-weight: bold;
        }

        input[type="date"], input[type="text"], select {
            padding: 5px;
            font-size: 16px;
        }

        .submit-section {
            margin-left: 10px;
        }

        .submit-section button {
            background-color: #0073e6;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
        }

        .submit-section button:hover {
            background-color: #005bb5;
        }
    </style>
</head>
<body>
<header>
    <div class="header-content">
        <img src="logo.png" alt="College Logo" class="logo">
        <h1>Vidyalankar Institute of Technology</h1>
        <div class="header-buttons">
            <?php if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                <button onclick="location.href='logout.php'">Logout</button>
            <?php else: ?>
                <button onclick="location.href='login.php'">Login</button>
            <?php endif; ?>
        </div>
    </div>
</header>

<main>
    <div class="container">
        <h2>Select Details</h2>
        <form action="attendance.php" method="POST">
            <div class="dropdown-section">
                <label for="branch">Branch:</label>
                <select id="branch" name="branch">
                    <option value="">Select Branch</option>
                    <!-- Options populated dynamically -->
                </select>
            </div>
            <div class="dropdown-section">
                <label for="semester">Semester:</label>
                <select id="semester" name="semester">
                    <option value="">Select Semester</option>
                    <!-- Options populated dynamically -->
                </select>
            </div>
            <div class="dropdown-section">
                <label for="course">Course:</label>
                <select id="course" name="course">
                    <option value="">Select Course</option>
                    <!-- Options populated dynamically -->
                </select>
            </div>
            <div class="dropdown-section">
                <label for="division">Division:</label>
                <select id="division" name="division">
                    <option value="">Select Division</option>
                    <!-- Options populated dynamically -->
                </select>
            </div>
            <div class="dropdown-section">
                <label for="faculty">Faculty:</label>
                <select id="faculty" name="faculty">
                    <option value="">Select Faculty</option>
                    <!-- Options populated dynamically -->
                </select>
            </div>
            <div class="submit-section">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</main>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const branchSelect = document.getElementById('branch');
    const semesterSelect = document.getElementById('semester');
    const courseSelect = document.getElementById('course');
    const divisionSelect = document.getElementById('division');
    const facultySelect = document.getElementById('faculty');

    // Function to handle populating dropdowns
    function populateDropdown(selectElement, data) {
        selectElement.innerHTML = '<option value="">Select</option>';
        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item.name;
            option.text = item.name;
            selectElement.add(option);
        });
    }

    // Fetch and populate branches
    fetch('get-branches.php')
        .then(response => response.json())
        .then(data => populateDropdown(branchSelect, data))
        .catch(error => console.error('Error fetching branches:', error));

    // Fetch and populate semesters
    fetch('get-semesters.php')
        .then(response => response.json())
        .then(data => populateDropdown(semesterSelect, data))
        .catch(error => console.error('Error fetching semesters:', error));

    branchSelect.addEventListener('change', function() {
        populateDropdown(courseSelect, []); // Clear courses on branch change
        populateDropdown(facultySelect, []); // Clear faculties on branch change
    });

    semesterSelect.addEventListener('change', function() {
        populateDropdown(courseSelect, []); // Clear courses on semester change
        populateDropdown(facultySelect, []); // Clear faculties on semester change
    });

    // Fetch and populate courses based on branch and semester selection
    function fetchAndPopulateCourses() {
        const branchName = branchSelect.value;
        const semesterName = semesterSelect.value;

        if (branchName && semesterName) {
            fetch(`get-courses.php?semester_name=${semesterName}&branch_name=${branchName}`)
                .then(response => response.json())
                .then(data => populateDropdown(courseSelect, data))
                .catch(error => console.error('Error fetching courses:', error));
        }
    }

    branchSelect.addEventListener('change', fetchAndPopulateCourses);
    semesterSelect.addEventListener('change', fetchAndPopulateCourses);

    // Fetch and populate divisions
    fetch('get-divisions.php')
        .then(response => response.json())
        .then(data => populateDropdown(divisionSelect, data))
        .catch(error => console.error('Error fetching divisions:', error));

    // Fetch and populate faculties based on course and branch selection
    courseSelect.addEventListener('change', function() {
        const courseName = courseSelect.value;
        const branchName = branchSelect.value;

        if (courseName && branchName) {
            fetch(`get-faculty.php?branch_name=${branchName}`)
                .then(response => response.json())
                .then(data => populateDropdown(facultySelect, data))
                .catch(error => console.error('Error fetching faculties:', error));
        }
    });
});

</script>
</body>
</html>
