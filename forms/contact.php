<?php
session_start();
if(isset($_SESSION['id_user'])){
    $user_id = $_SESSION['id_user'];
} else {
    // handle the error, e.g. redirect to an error page
    $user_id = 0;
}
include("db.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';

// Set error reporting level to 0 to prevent any output from being displayed on the screen.
error_reporting(0);

// Only process POST requests.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // Instantiate PHPMailer object.
    $mail = new PHPMailer(true);

    try {
        // Set up SMTP authentication and encryption
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'YOUR_MAIL@gmail.com';   ///change this with your email address
        $mail->Password = 'YOUR_PASSWORD';  //change this with email app password
        $mail->SMTPSecure = '';
        $mail->Port = 587;

        // Set up email message.
        $mail->setFrom($email, $name);
        $mail->addAddress('codestomp@gmail.com');  // add your designated email addess(codestomp@gmail.com)
        $mail->Subject = $subject;
        $mail->Body = $message;

        // Insert data into the database.
        $query = "INSERT INTO `mailbox`(`id_fromuser`, `fromuser`, `id_touser`, `subject`, `message`) VALUES ('$user_id', '$name', '$email', '$subject', '$message')";
        $result = mysqli_query($conn, $query);
        // Send the email.
        @$mail->send();

        // handle the submission message by your own
    } catch (Exception $e) {
        // Log any errors or exceptions instead of displaying them on the screen.
        echo 'Message could not be sent. Error: ', $mail->ErrorInfo;
    }
}
