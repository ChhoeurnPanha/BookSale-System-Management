<?php
session_start();
include "Mysql.php";

$message = "";

if(isset($_POST['login'])){

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if($email == "" || $password == ""){
        $message = "All fields are required!";
    } else {

        $stmt = $conn->prepare("SELECT user_id, username, email, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if($result->num_rows > 0){

            $row = $result->fetch_assoc();

            if(password_verify($password, $row['password'])){

                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['username'] = $row['username'];

                header("Location: dashboard.php");
                exit();

            } else {
                $message = "Wrong Password!";
            }

        } else {
            $message = "Email Not Found!";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body{
            font-family: Arial;
            background-image: url(https://i.pinimg.com/1200x/89/2b/60/892b60d11eb60cfdb951e156f242e233.jpg);
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            display:flex;
            justify-content:center;
            align-items:center;
            height:100vh;
        }

        .box{
            background:white;
            padding:40px;
            width:320px;
            border-radius:10px;
            box-shadow:0 10px 20px rgba(0,0,0,0.2);
            text-align:center;
        }

        input{
            width:100%;
            padding:10px;
            margin:10px 0;
            border-radius:5px;
            border:1px solid #ccc;
        }

        button{
            width:100%;
            padding:10px;
            background:#667eea;
            color:white;
            border:none;
            border-radius:5px;
            cursor:pointer;
        }

        button:hover{
            background:#5a67d8;
        }

        a{
            display:block;
            margin-top:15px;
            text-decoration:none;
            color:#667eea;
        }

        .error{
            color:red;
            margin-top:10px;
        }
    </style>
</head>

<body>

<div class="box">
    <h2>Login</h2>

    <form method="POST">
        <input type="text" name="username" placeholder="Enter username" required>
        <input type="email" name="email" placeholder="Enter Email" required>
        <input type="password" name="password" placeholder="Enter Password" required>
        <button name="login">Login</button>
    </form>

    <?php if(isset($message)) echo "<div class='error'>$message</div>"; ?>

    <a href="register.php">Don't have account? Register</a>
</div>

</body>
</html>
