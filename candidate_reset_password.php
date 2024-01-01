
<?php
include("db.php");
session_start();
$code = $_SESSION['reset_code'];
$email= $_SESSION['reset_email'];
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code_given = $_POST['code'];
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $encrypted_password = base64_encode(strrev(md5($password)));
    if ($code_given == $code) {
        //code match, update user's password.
        $sql = "UPDATE users SET password = '$encrypted_password' WHERE email = '$email'";
        mysqli_query($conn, $sql);

        // Display success message
        echo '<script>alert("Password updated successfully.");</script>';

        // Redirect the user to the login page.
        header('Location:login-candidates.php');
        exit();
    } else {
        //code do not match.
        $error_message = 'Invalid code.';
        header('location:candidate_forget_password.php');
    }
}
?>


