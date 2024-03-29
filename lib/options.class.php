<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
/* Provides some options to check validate or filter vars */

class Options
{
    /*
     * Checks if an email is valid
     * @param  string  $mail the value to be checked
     * @return boolean       true if it's valid
     */
    public function isValidMail($mail) {
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            return FALSE;
        }
        if (!checkdnsrr("gmail.com", "MX")) { // if we have no internet access on the server
            return TRUE;
        }
        list($username, $maildomain) = explode("@", $mail);
        if (checkdnsrr($maildomain, "MX")) {
            return TRUE;
        }
        return FALSE;
    }

    public function sendMail($to, $toName, $from, $fromName, $subject, $message, $domain, $isHtml = true) {
        
        require MLS_ROOT.'/PHPMailer/Exception.php';
        require MLS_ROOT.'/PHPMailer/PHPMailer.php';
        require MLS_ROOT.'/PHPMailer/SMTP.php';

        $mail = new PHPMailer(true);
        
        try
        {   
            if($from == ""){
                $from = 'no.reply@'.$domain;
            }           
            
            //Server settings
            $mail->SMTPDebug = 0;                                   // Enable verbose debug output
            $mail->isSMTP();                                        // Set mailer to use SMTP
            $mail->Host = 'aspmx.l.google.com';                     // Specify main and backup SMTP servers
            $mail->Port = 25 ;                                      // TCP port to connect to            
            //$mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = 'no.reply@dot.com';                   // SMTP username
            $mail->Password = '';                                   // SMTP password                       
            
            //Recipients
            $mail->setFrom($from, $fromName);
            $mail->addAddress($to, $toName);                        // Add a recipient            
            // $mail->addReplyTo('no.reply@'.$domain, 'No Reply');            
            
            //Content
            $mail->isHTML(true);                                    // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $message;            
            
            $mail->send();
            
            return TRUE;
        } catch (Exception $ex) {            
            echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
            return FALSE;
        }
    }

    /**
     * Encodes the string with htmlentities
     * @param  string $string the string to be encoded
     * @return string         encoded string
     */
    public function html($string) {
        return htmlentities($string, ENT_QUOTES);
    }

    /**
     * Time elapes since a times
     * @param  int $time The past time
     * @return string       time elapssed
     * credits: http://stackoverflow.com/a/2916189/1579481
     */
    public function tsince($time, $end_msg = 'ago') {

        $time = abs(time() - $time); // to get the time since that moment

        if($time == 0)
            return "Just now";

        $tokens = array (
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        );

        foreach ($tokens as $unit => $text) {
            if ($time < $unit) continue;
            $numberOfUnits = floor($time / $unit);
            return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'').' '. $end_msg;
        }
    }

    /**
     * It will show the error message and kill the process.
     * @param  string $error The error to be displayed !
     * @return void
     */
    public function fError($error) {
        global $set;
        echo "<div style='margin:0 auto;text-align:center;width:80%'><div class='alert alert-danger'>$error</div></div>";
        include "./layout/footer.php";
        die();
    }

    /**
     * It will display the error
     * @param  string $error the error to be displayed
     * @param  int $return if 1 it will return instead of echo it !
     * @return void        
     */
    public function error($error='', $return = 0) {
        $html = "<div class='container'>"
              . "<div class='alert alert-danger'>"
              . "$error"
              . "</div>"
              . "</div>";
        if($return)
            return $html;
        echo $html;
    }

    /**
     * It will show a success message
     * @param  string $message the message to be displayed !
     * @param  int $return if 1 it will return instead of echo it !
     * @return void
     */
    public function success($message='', $return = 0) {
        $html = "<div class='container'>"
              . "<div class='alert alert-success'>"
              . $message
              . "</div>"
              . "</div>";
        if ($return) {
            return $html;
        }

        echo $html;
    }
    /**
     * It will show an info message
     * @param  string $message the message to be displayed !
     * @param  int $return if 1 it will return instead of echo it !
     * @return void
     */
    public function info($message='', $return = 0) {
        $html = "<div class=\"alert alert-info\">".$message."</div>";
        if ($return) {
            return $html;
        }

        echo $html;
    }

    /**
     * it will return the query string as hidden fields or as url
     * @param  string $type   type of output
     * @param  array  $ignore ignored elements
     * @return string         
     */
    public function queryString($type = '', $ignore = array()) {
        $result = '';
        foreach($_GET as $k => $v) {
            if (in_array($k, $ignore)) {
                continue;
            }

            if($type == 'hidden') {
                    $result .= "<input type='hidden' name='".urlencode($k)."' value='".urlencode($v)."'>";
            } else {
                    $result[] = urlencode($k)."=".urlencode($v);
            }
        }

        if (is_array($result)) {
            return "?" . implode("&", $result);
        }

        return $result;
    }

    /**
     * removes underlines and adds uppercase to words 
     * @param  string $text the string to be converted
     * @return string       converted string
     */
    public function prettyPrint($text) {
        return str_replace("_", " ", ucfirst($text));
    }

    /**
     * checks if the given value is a valid username
     * @param  string $value the value to be checked
     * @return bool        true if it's a match
     */
    public function validUsername($value) {
        return preg_match("/^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/", $value);
    }

    /**
     * checks if the given value is a valid password
     * @param  string $value the value to be checked
     * @return bool        true if it's a match
     */
    public function validPassword($value) {
        
        
        
        return preg_match("/^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/", $value);
    }

}

