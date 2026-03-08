<?php
include "Mysql.php";

$message = "";

if(isset($_POST['register'])){

    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if($email == "" || $username == "" || $password == ""){
        $message = "All fields are required!";
    } else {

        // Check if email already exists
        $check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if($check->num_rows > 0){
            $message = "Email already exists!";
        } else {

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashedPassword);

            if($stmt->execute()){
                $message = "Register Success! Go to Login.";
            } else {
                $message = "Something went wrong!";
            }

            $stmt->close();
        }

        $check->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
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
            background:#11998e;
            color:white;
            border:none;
            border-radius:5px;
            cursor:pointer;
        }

        button:hover{
            background:#0f8a7c;
        }

        a{
            display:block;
            margin-top:15px;
            text-decoration:none;
            color:#11998e;
        }

        .msg{
            margin-top:10px;
            color:green;
        }
    </style>
</head>

<body>

<div class="box">
    <h2>Register</h2>

    <form method="POST">
        <input type="text" name="username" placeholder="Enter username" required>
        <input type="email" name="email" placeholder="Enter Email" required>
        <input type="password" name="password" placeholder="Enter Password" required>
        <button name="register">Register</button>
    </form>

    <?php if(isset($message)) echo "<div class='msg'>$message</div>"; ?>

    <a href="index.php">Already have account? Login</a>
</div>

</body>
</html>
