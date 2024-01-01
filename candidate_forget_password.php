<?php
include("db.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';

// Only process POST requests.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    
    // Check if email exists in the database.
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        // Email exists in the database, generate a code and send email.
        $code = rand(1000, 9999);
    
       // Set the code and email in the session.
       session_start();
       $_SESSION['reset_code'] = $code;
       $_SESSION['reset_email'] = $email;

        // Instantiate PHPMailer object.
        $mail = new PHPMailer(true);
            // Set up SMTP authentication and encryption
            $mail->isSMTP();
            $mail->SMTPDebug = 0;
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'YOUR_MAIL@gmail.com';  //set your email
            $mail->Password = 'YOUR_PASSWORD';  //set your app password
            $mail->SMTPSecure = '';
            $mail->Port = 587;

            // Set up email message.
            $mail->setFrom('mirzaabbasuddin2@gmail.com', 'Placement Portal'); //set your website email
            $mail->addAddress($email);
            $mail->Subject = 'Password Reset Code';
            $mail->Body = "Your password reset code is: $code";

            // Send email.
            $mail->send();
            $show_code_form = true;
    } else {
        // Email does not exist in the database.
        $show_email_form = true;
        $error_message = 'Enter the valid email.';
    }
} else {
    $show_email_form = true;
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Forgot Password</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        .form-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        form {
            width: 400px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label,
        input {
            display: block;
            margin-bottom: 10px;
            width: 400px;
        }

        input[type="submit"] {
            background-color: #008CBA;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 3px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #006B8F;
        }
    </style>
</head>

<body>
    <div class="form-wrapper">
       <?php if (isset($show_code_form) && $show_code_form) { ?>
            <form action="candidate_reset_password.php" method="post">
                <h2>Reset Password</h2>
                <p>Please enter the code sent to your email address and a new password below.</p>
                <input type="hidden" name="email" value="<?= $email ?>">
                <label for="code">Code:</label>
                <input type="text" id="code" name="code" required>
                <label for="password">New Password:</label>
                <input type="password" id="password" name="password" required>
                <input type="submit" value="Reset Password">
            </form>
        <?php } else { ?>
            <form action="#" method="post">
                <h2>Forgot Password</h2>
                <p>Please enter your email address below and we'll send you a code to reset your password.</p>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                <input type="submit" value="Send">
                <?php if (isset($error_message)) { ?>
                    <p style="color: red;"><?= $error_message ?></p>
                <?php } ?>
            </form>
        <?php } ?>
    </div>
</body>

</html>