<?php
session_start();
include 'database/dbcon.php';

if (isset($_POST['Submit-SignUp'])) {
    $signUpUserName = $_POST['signUpUsername'];
    $signUpEmail = $_POST['signUpUserEmail'];
    $signUpPassword = $_POST['signUpUserPassword'];

    // Check if the password meets the length requirement
    if (strlen($signUpPassword) < 6) {
        $_SESSION['error'] = "Password must be at least 6 characters long";
        header('Location: login.php');
        exit;
    }

    // Additional checks for username and password match
    if ($signUpUserName === $signUpPassword) {
        $_SESSION['error'] = "Username and password cannot match";
        header('Location: login.php');
        exit;
    }

    // Check if the email is already registered
    try {
        $existingUser = $auth->getUserByEmail($signUpEmail);
        $_SESSION['error'] = "Email already exists. Please go to the login page or reset your password.";
        header('Location: login.php');
        exit;
    } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
    }

    // Create an array with user properties including the display name
    $userProperties_signup = [
        'email' => $signUpEmail,
        'password' => $signUpPassword,
        'displayName' => $signUpUserName,
    ];

    try {
        // Create the user with email, password, and display name
        $createdUser = $auth->createUser($userProperties_signup);
    
        if ($createdUser) {
            $_SESSION['status'] = "User Created Successfully. Please log in.";
            $_SESSION['email'] = $signUpEmail;
            $_SESSION['start'] = true;
            header('Location: home.php');
            exit;
        } else {
            $_SESSION['error'] = "User Creation Failed";
            header('Location: login.php');
            exit;
        }
    } catch (\Kreait\Firebase\Exception\Auth\EmailExists $e) {
        // Handle the case where the email already exists
        $_SESSION['error'] = "Email already exists. Please go to the login page or reset your password.";
        header('Location: login.php');
        exit;
    } catch (Exception $e) {
        // Handle other exceptions
        $_SESSION['error'] = "An error occurred during user creation";
        header('Location: login.php');
        exit;
    }
}
?>
