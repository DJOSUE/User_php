<?php

/*
 * MASTER LOGIN SYSTEM
 * @author David Huaraca
 * May 2019
 */


include "inc/init.php";
include 'lib/captcha/captcha.php';

if ($user->islg()) { // if it's alreadt logged in redirect to the main page
    header("Location: $set->url");
    exit;
}

$page->title = "Register to " . $set->site_name;

// determine if captcha code is correct
$captcha = ((!$set->captcha) || ($set->captcha && isset($_SESSION['captcha']) && isset($_POST['captcha']) && ($_SESSION['captcha']['code'] === $_POST['captcha'])));

//Values
$username = "";
$firstname = "";
$lastname = "";
$display_name = "";
$email = "";
$password = "";

if ($_POST && isset($_SESSION['token']) && ($_SESSION['token'] == $_POST['token']) && $set->register && $captcha) {

    // we validate the data
    $username = $_POST['username'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $display_name = $_POST['display_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];


    if (!isset($username[3]) || isset($username[30])) {
        $page->error = "Username too short or too long !";
    }

    if (!$options->validUsername($username)) {
        $page->error = "Username, only letters (a-z), and numbers (0-9) are allowed.";
    }

    if (!isset($display_name[3]) || isset($display_name[50])) {
        $page->error = "Display name too short or too long !";
    }

    if (!isset($password[3]) || isset($password[30])) {
        $page->error = "Password too short or too long !";
    }

    if (!$options->isValidMail($email)) {
        $page->error = "Email address is not valid.";
    }

    if ($db->getRow("SELECT `userid` FROM `" . MLS_PREFIX . "users` WHERE `username` = ?s", $username)) {
        $page->error = "Username already in use !";
    }

    if ($db->getRow("SELECT `userid` FROM `" . MLS_PREFIX . "users` WHERE `email` = ?s", $email)) {
        $page->error = "Email already in use !";
    }


    if (!isset($page->error)) {
        $user_data = array(
            "username" => $username,
            "firstname" => $username,
            "lastname" => $username,
            "display_name" => $display_name,
            "password" => sha1($password.$username),
            "email" => $email,
            "lastactive" => time(),
            "regtime" => time(),
            "validated" => 1
        );

        if ($set->email_validation == 1) {

            $user_data["validated"] = $key = sha1(rand());

            $link = $set->url . "/validate.php?key=" . $key . "&username=" . urlencode($username);
            
            $url_info = parse_url($set->url);
            $from = "From: not.reply@" . $url_info['host'];
            $sub = "Activate your account";
            $msg = "<div style='width:500px; margin-left:50px;' >"
                 . "Hello '$options->html($display_name), <br/><br/>"
                 . "Thank you for sign in. Help us secure your account by verifying your email address (-email)<br/><br/> "
                 . "<div style='text-align:center;font-size:10px'>"
                 . "<a style='font-size:15px;background-color:#007ac1; color:white; border:none; padding:10px 20px; text-align:center; display:inline-block; text-decoration:none;' href='$link' target='_blank'>Verify email address</a><br>"
                 . "If you can't access copy and paste this link to your browser.<br/>"
                 . "$link"
                 . "</div>"
                 . "Regards<br>"
                 . "<small>Note: You’re receiving this email because you recently created a new account with us. If this wasn’t you, please ignore this email.</small>"
                 . "</div>";
            
            if (!$options->sendMail($email, $sub, $msg, $from)) {
                // if we can't send the mail by some reason we automatically activate the account
                $user_data["validated"] = 1;
            }
        }

        if (($db->query("INSERT INTO `" . MLS_PREFIX . "users` SET ?u", $user_data)) && ($id = $db->insertId())) {
            $page->success = 1;
            $_SESSION['user'] = $id; // we automatically login the user
            $user = new User($db);
        } else {
            $page->error = "There was an error ! Please try again !";
        }
    }
} else if ($_POST) {    
    
    $username = $_POST['username'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $display_name = $_POST['display_name'];
    $email = $_POST['email'];    
    
    if (!$captcha) {
        $page->error = "Invalid captcha code !";
    } else {
        $page->error = "Invalid request !";
    }
}

include './layout/header.php';

if (!$set->register) { // we check if the registration is enabled
    $options->fError("We are sorry registration is blocked momentarily please try again leater !");
}

$_SESSION['token'] = sha1(rand()); // random token

if ($set->captcha) {
    $_SESSION['captcha'] = captcha();
}

$extra_content = ''; // holds success or error message

if (isset($page->error)) {
    $extra_content = $options->error($page->error);
}

if (isset($page->success)) {
    echo "
<div class='container'>
    <h1>Congratulations !</h1>";
    $options->success("<p><strong>Your account was successfully registered !</strong></p>");
    echo " <a class='btn btn-primary' href='$set->url'>Start exploring</a>    
</div>";
} else {

    if ($set->captcha) {
        $captcha = "
    <div class='form-group'>
        <label for='captcha'>Enter the code:</label><br/>
        <img src='" . $_SESSION['captcha']['image_src'] . "'> <br/>
        <input type='text' class='form-control' name='captcha' id='captcha' required>    
    </div>";
    } else {
        $captcha = '';
    }

    echo "
    <div class='container'>
        <div class='row justify-content-md-center'>
        " . $extra_content . "
        <form action='#' id='contact-form' class='form-horizontal well' method='post'>
            <fieldset class='scheduler-border'>
            <legend class='scheduler-border'>Register Form </legend>
                <div class='form-row'>
                    <div class='col-md-4 mb-3'>
                        <label for='firstname'>First name</label>
                        <input id='firstname' name='firstname' type='text' class='form-control' placeholder='First name' value='$firstname' required>
                    </div>
                    <div class='col-md-4 mb-3'>
                        <label for='lastname'>Last name</label>
                        <input id='lastname' name='lastname' type='text' class='form-control' placeholder='Last name' value='$lastname' required>
                    </div>
                    <div class='col-md-4 mb-3'>
                        <label for='username'>Username</label>
                        <div class='input-group'>
                            <div class='input-group-prepend'>
                                <span class='input-group-text' id='inputGroupPrepend2'>@</span>
                            </div>
                            <input id='username' name='username' type='text' class='form-control' placeholder='Username' aria-describedby='inputGroupPrepend2' value='$username' required>
                        </div>
                    </div>
                </div>
                <div class='form-row'>
                    <div class='col-md-4 mb-3'>
                        <label for='display_name'>Display name</label>
                        <input id='display_name' name='display_name' type='text' class='form-control' placeholder='Display name' value='$display_name' required>
                    </div>
                    <div class='col-md-4 mb-3'>
                        <label for='email'>Email</label>
                        <input id='email' name='email' type='text' class='form-control' placeholder='Email' value='$email' required>
                    </div>
                    <div class='col-md-4 mb-3'>
                        <label class='control-label' for='password'>Password</label>
                        <input id='password' name='password' type='password' class='form-control' required>
                    </div>
                </div>
                <div class='form-row'>                    
                    <div class='col-md-4 mb-3'>
                        $captcha
                    </div>
                </div>         
          
          <input type='hidden' name='token' value='" . $_SESSION['token'] . "'>
          
          <div class='form-actions'>
          <button type='submit' class='btn btn-primary btn-large'>Register</button>
            <button type='reset' class='btn'>Reset</button>
          </div>
        </fieldset>
      </form>
    </div>


  </div>";
}

include "./layout/footer.php";
