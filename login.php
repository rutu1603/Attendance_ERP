
<?php
session_start();
include "connection.php"; // Ensure this path is correct

if (isset($_POST["login"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Prepare the SQL statement to fetch user based on email and password
    $sql = "SELECT * FROM login WHERE email = ? AND password = ?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        // Bind parameters to the prepared statement
        mysqli_stmt_bind_param($stmt, "ss", $email, $password);
        // Execute the prepared statement
        mysqli_stmt_execute($stmt);
        // Get the result
        $result = mysqli_stmt_get_result($stmt);
        // Fetch user data
        $user = mysqli_fetch_assoc($result);

        if ($user) {
            // User found, set session variable and redirect to index.php
            $_SESSION['logged_in'] = true;
            header("Location: index.php");
            exit(); // Terminate script execution after redirection
        } else {
            // User not found or password incorrect
            echo "<div class='alert alert-danger'>Invalid email or password</div>";
        }
    } else {
        // Error in preparing SQL statement
        echo "Error: " . mysqli_error($conn);
    }

    // Close the prepared statement
    mysqli_stmt_close($stmt);
}

// Close the database connection
mysqli_close($conn);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    
    <style>
        .body {
            background-color: blue;
        }
        .container {
            max-width: 600px;
            position: absolute;
            top: 30%;
            left: 40%;
            margin-top: -50px; /* this is half the height of your div */
            margin-left: -100px;
            padding: 50px;
            background-color: white;
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
        }
        .form-group {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
      <form action="login.php" method="post">
        <h2 style="text-align:center">Login</h2>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" placeholder="Enter Email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" placeholder="Enter Password" name="password" class="form-control" required>
        </div>
        <div class="form-btn">
            <input type="submit" value="Login" name="login" class="btn btn-primary">
        </div>
      </form>
    </div>
</body>
</html>
