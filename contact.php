<?php
/*
 * MASTER LOGIN SYSTEM
 * @author David Huaraca
 * May 2019
 */

include "inc/init.php";

$page->title = "Contact to " . $set->site_name;

$presets->setActive("contact"); // we highlith the contact link

if ($_POST && isset($_SESSION['token']) && ($_SESSION['token'] == $_POST['token'])) {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    
    $to = $set->support_email;
    $toName ="Support";
    $domain = $set->domain;

    if (!$options->isValidMail($email)) {
        $page->error = "Email address is not valid.";
    } else if (!isset($message[10])) {
        $page->error = "Message was too short !";
    } else {
        $from = "From: " . $email;
        $sub = "Contact $set->site_name !";        
        if ($options->sendMail($to, $toName, $email, $name, $sub, $message, $domain)) {
            $page->success = "Your message was sent !";
        }
    }
} else if ($_POST) {
    $page->error = "Invalid request !";    
}

include './layout/header.php';

$_SESSION['token'] = sha1(rand()); // random token

echo "
<div class='container'>";

    if (isset($page->error)) {
        $options->error($page->error);
    } else if (isset($page->success)) {
        $options->success($page->success);
    }
    echo "

    <form action='#' method='post'>

        <h3>Contact</h3>

        <div class='form-group'>
            <label for='name'>Your Name</label>
            <input id='name' name='name' type='text' class='form-control'>
        </div>

        <div class='form-group'>
            <label for='email'>Your Email</label>
            <input id='email' name='email' type='text' class='form-control'>
        </div>

        <div class='form-group'>
            <label for='email'>Message</label>
            <textarea name='message' rows='5' class='form-control'></textarea>
        </div>
        
        <input type='hidden' name='token' value='".$_SESSION['token']."'>
        
        <button type='submit' id='submit' class='btn btn-primary'>Send</button>
    </form>
</div>
";

include './layout/footer.php';
