<?php

/*
 * MASTER LOGIN SYSTEM
 * @author David Huaraca
 * May 2019
 */

include "inc/init.php";

if ($user->islg()) { // if it's alreadt logged in redirect to the main page 
    header("Location: $set->url");
    exit;
}

$page->title = "Login to " . $set->site_name;

if ($_POST && isset($_SESSION['token']) && ($_SESSION['token'] == $_POST['token'])) 
{
    // we validate the data
    if (isset($_GET['forget'])) {

        $email = $_POST['email'];

        if (!$options->isValidMail($email)) {
            $page->error = "Email address is not valid.";
        }

        if (!isset($page->error) && !($usr = $db->getRow("SELECT `userid` FROM `" . MLS_PREFIX . "users` WHERE `email` = ?s", $email))) {
            $page->error = "<p class='text-center' >"
                         . "<b>Thank you</b> <br/>"
                         . "Your request has been processed. You will receive an e-mail with instructions for changing your password. <br/> "
                         . "If you do not receive an email within the next hour, the email address you provided may not match our records."
                         . "<p/>";
        }

        if (!isset($page->error)) {
            $key = sha1(rand());

            $db->query("UPDATE `" . MLS_PREFIX . "users` SET `key` = ?s WHERE `userid` = ?i", $key, $usr->userid);
            
            $from = $set->support_email;
            $domain = $set->domain;
            $fromName = "Support ".$set->site_name;
            
            $link = $set->url . "/login.php?key=" . $key . "&userid=" . $usr->userid;
            
            $sub = "Password Reset Request";
            $msg = "<div style='width:500px; margin-left:50px;' >"
                 . "<p align='left'> Hello, </p>"
                 . "<p align='left'> A request was submitted to reset the password of your account.</p>"
                 . "<div style='text-align:center;font-size:10px'>"
                 . "<a style='font-size:15px;background-color:#007ac1; color:white; border:none; padding:10px 20px; text-align:center; display:inline-block; text-decoration:none;' href='$link' target='_blank'>Set new password</a><br>"                 
                 . "If you can't access copy and paste this link to your browser.<br/>"
                 . "$link"
                 . "</div>"
                 . "<p align='left'>Regards</p>"
                 . "<small align='left'>Note: If you did not make this request, feel free to delete this email and no action will be taken. Dont reply to this email.</small>"                 
                 . "</div>"
                 . "</div>";
            if ($options->sendMail($email, '', $from, $fromName, $sub, $msg, $domain))
                $page->success = "<p class='text-center' >"
                         . "<b>Thank you</b> <br/>"
                         . "Your request has been processed. You will receive an e-mail with instructions for changing your password. <br/> "
                         . "If you do not receive an email within the next hour, the email address you provided may not match our records."
                         . "<p/>";
        }
    } 
    else if (isset($_GET['key'])){
        if ($_GET['key'] == '0') 
        {
            header("Location: $set->url");
            exit;
        }
        if ($usr = $db->getRow("SELECT `userid` FROM `" . MLS_PREFIX . "users` WHERE `key` = ?s", $_GET['key'])) {
            if ($db->query("UPDATE `" . MLS_PREFIX . "users` SET `password` = ?s WHERE `userid` = ?i", sha1($_POST['password']), $usr->userid)) {
                $db->query("UPDATE `" . MLS_PREFIX . "users` SET `key` = '0' WHERE `userid` = ?i", $usr->userid);
                $page->success = "Password was updated !";
            }
        }
    } 
    else {
        $name = $_POST['name'];
        $password = $_POST['password'];


        if (!($usr = $db->getRow("SELECT `userid` FROM `" . MLS_PREFIX . "users` WHERE `username` = ?s AND `password` = ?s", $name, sha1($password))))
            $page->error = "Username or password are wrong !";
        else {
            if ($_POST['r'] == 1) {
                $path_info = parse_url($set->url);
                setcookie("user", $name, time() + 3600 * 24 * 30, $path_info['path']); // set
                setcookie("pass", sha1($password), time() + 3600 * 24 * 30, $path_info['path']); // set
            }
            $_SESSION['user'] = $usr->userid;
            header("Location: $set->url");
            exit;
        }
    }
} else if ($_POST) {
    $page->error = "Invalid request !";
}

include './layout/header.php';

$_SESSION['token'] = sha1(rand()); // random token

echo "
<div class='container'>
    ";


if (isset($page->error)) {
    $options->error($page->error);
} else if (isset($page->success)) {
    $options->success($page->success);
}

if (isset($_GET['forget'])) {

    echo "
    <div class='row justify-content-md-center'>
        <form action='#' method='post'>        
            <h3>Recover</h3>
            <br/>
            <div class='form-group'>              
                <label for='email'>Email</label>              
                <input type='text' placeholder='john.doe@domain.com' name='email' class='form-control'>              
            </div>
            
            <input type='hidden' name='token' value='" . $_SESSION['token'] . "'>

            <div class='form-group'>
                <button type='submit' id='submit' class='btn btn-primary'>Recover</button>              
            </div>
          ";   
            
    
} else if (isset($_GET['key']) && !isset($page->success)) {
    if ($_GET['key'] == '0') {
        echo "<div class=\"alert alert-danger\">Error !</div>";
        exit;
    }
    if ($usr = $db->getRow("SELECT `userid` FROM `" . MLS_PREFIX . "users` WHERE `key` = ?s AND `userid` = ?i", $_GET['key'], $_GET['userid'])) {
        echo "
    <div class='row justify-content-md-center'>
        <form class='form-horizontal well' action='#' method='post'>        
            <h3>Reset</h3>
            <br/>
            <div class='form-group'>
                <label>New password</label>
                <input type='password' name='password' class='form-control'>              
            </div>

            <input type='hidden' name='token' value='" . $_SESSION['token'] . "'>

            <button type='submit' id='submit' class='btn btn-primary'>Save</button>
              
        ";
    } else {
        echo "<div class=\"alert alert-danger\">Error bad key !</div>";
    }
} else {

    echo "
    <div class='row justify-content-md-center'>
        <form class='form-horizontal well' action='?' method='post'>        
            <h3>Login Form</h3>
            <br/>
            <div class='form-group'>              
                <label for='name'>Username</label>
                <input name='name' type='text' placeholder='john.doe' class='form-control'>
            </div>

            <div class='form-group'>
                <label for='password'>Password</label>
                <input name='password' type='password' placeholder='type your password' class='form-control'>
            </div>
            
            <div class='form-group'>
                <label for='r'>Remember Me</label>              
                <input name='r' type='checkbox' value='1' id='r'>              
            </div>

            <input type='hidden' name='token' value='" . $_SESSION['token'] . "'>

            <div class='form-group'>
                <button type='submit' id='submit' class='btn btn-primary'>Sign in</button>
                <a href='?forget=1' class='btn btn-secondary'>Forgot Password</a>              
            </div>
          ";
}

echo "  </form>
    </div>
</div>";


include "./layout/footer.php";