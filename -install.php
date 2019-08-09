<?php
/*
 * MASTER LOGIN SYSTEM
 * @author David Huaraca
 * May 2019
 */

define("MLS_ROOT", dirname(dirname(__FILE__))); // the root path

include 'lib/mysql.class.php';
include 'lib/options.class.php';

$options = new Options;
$page = new stdClass();

if ($_POST) {
    // we first check the settings file
    if (!is_writable('inc/settings.php')) {
        chmod('inc/settings.php', 0666);
    }

    // we make the db connection
    $db = new SafeMySQL(array(
        'host' => $_POST['dbhost'],
        'user' => $_POST['dbuser'],
        'pass' => $_POST['dbpass'],
        'db' => $_POST['dbname']));


    // once that is done we write the details in the settings file
    $host = str_replace("'", "\'", $_POST['dbhost']);
    $user = str_replace("'", "\'", $_POST['dbuser']);
    $pass = str_replace("'", "\'", $_POST['dbpass']);
    $name = str_replace("'", "\'", $_POST['dbname']);
    $prefix = str_replace("'", "\'", $_POST['tbprefix']);

    $data = <<<EEE
<?php

/*
 * MASTER LOGIN SYSTEM
 * @author David Huaraca
 * May 2019
 */


// database details
\$set->db_host = '$host'; // database host
\$set->db_user = '$user'; // database user
\$set->db_pass = '$pass'; // database password
\$set->db_name = '$name'; // database name

define('MLS_PREFIX', '$prefix');  

EEE;

    // add the data to the file
    if (!file_put_contents('inc/settings.php', $data)) {
        $page->error = "There is an error with inc/settings.php make sure it is writable.";
    }

    $sqls[] = "CREATE TABLE IF NOT EXISTS `" . $prefix . "groups` ( 
                  `groupid` int(11) NOT NULL AUTO_INCREMENT,
                  `name` varchar(255) NOT NULL,
                  `type` int(11) NOT NULL,
                  `priority` int(11) NOT NULL,
                  `color` varchar(50) NOT NULL,
                  `canedit` int(11) NOT NULL,
                  `regstatus` CHAR(1) NOT NULL DEFAULT 'A',
                  PRIMARY KEY (`groupid`)
              ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";

    $sqls[] = " INSERT INTO `" . $prefix . "groups` (`groupid`, `name`, `type`, `priority`, `color`, `canedit`, `regstatus`) VALUES
                (1, 'ExternalUser', 0, 1, '', 0, 'A'),
                (2, 'InternalUser', 1, 1, '#08c', 0, 'A'),
                (3, 'Manager', 2, 1, 'green', 0, 'A'),
                (4, 'Administrator', 3, 1, '#F0A02D', 1, 'A');";

    $sqls[] = "CREATE TABLE IF NOT EXISTS `" . $prefix . "links` (
                  `linkid` int(11) NOT NULL,
                  `location` varchar(200) NOT NULL,
                  `name` varchar(50) NOT NULL,
                  `level` int(11) NOT NULL,
                  `parentid` int(11) NOT NULL,
                  `regstatus` CHAR(1) NOT NULL DEFAULT 'A',
                  UNIQUE KEY `linkid` (`linkid`)
               ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

    $sqls[] = "CREATE TABLE IF NOT EXISTS `" . $prefix . "grouplinks` (
                  `groupid` int(11) NOT NULL,
                  `linkid` int(11) NOT NULL,
                  `regstatus` CHAR(1) NOT NULL DEFAULT 'A',
                  FOREIGN KEY (groupid) REFERENCES " . $prefix . "groups(groupid),
                  FOREIGN KEY (linkid) REFERENCES " . $prefix . "links(linkid)
               ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

    $sqls[] = "CREATE TABLE IF NOT EXISTS `" . $prefix . "settings` (
                  `site_name` varchar(255) NOT NULL DEFAULT 'Demo Site',
                  `url` varchar(300) NOT NULL,
                  `domain` varchar(100) NOT NULL,
                  `admin_email` varchar(255) NOT NULL,
                  `support_email` varchar(255) NOT NULL,
                  `register` int(11) NOT NULL DEFAULT '1',
                  `email_validation` int(11) NOT NULL DEFAULT '0',
                  `captcha` int(11) NOT NULL
               ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

    $sqls[] = $db->parse(" INSERT INTO `" . $prefix . "settings` (`site_name`, `url`, `domain`, `admin_email`, `support_email`,`register`, `email_validation`, `captcha`)
                           VALUES (?s, ?s, ?s, ?s, ?s,1, 0, 1);", $_POST['sitename'], $_POST['siteurl'], $_POST['sitedomain'], $_POST['adminemail'], $_POST['supportemail']);

    $sqls[] = "CREATE TABLE IF NOT EXISTS `" . $prefix . "users` (
                  `userid` int(11) NOT NULL AUTO_INCREMENT,
                  `username` varchar(50) NOT NULL,
                  `firstname` varchar(50) NULL,
                  `lastname` varchar(50) NULL,
                  `display_name` varchar(255) NOT NULL,
                  `password` varchar(50) NOT NULL,
                  `email` varchar(255) NOT NULL,
                  `key` varchar(50) NOT NULL,
                  `validated` varchar(100) NOT NULL,
                  `groupid` int(11) NOT NULL DEFAULT '1',
                  `lastactive` int(11) NOT NULL,
                  `photo` varchar(50) NOT NULL DEFAULT 'default.png',                  
                  `regtime` int(11) NOT NULL,
                  `regstatus` CHAR(1) NOT NULL DEFAULT 'A',
                  PRIMARY KEY (`userid`)
              ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";

    $sqls[] = "INSERT INTO `" . $prefix . "users` (`userid`, `username`, `display_name`, `password`, `email`, `key`, `validated`, `lastactive`, `regtime`) 
               VALUES (1, 'admin', 'Admin', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'admin@gmail.com', '', '1', " . time() . ", " . time() . ");";

    foreach ($sqls as $sql) {
        if (!isset($page->error) && (!$db->query("?p", $sql))) {
            $page->error = "There was a problem while executing <code>$sql</code>";
        }
    }

    if (!isset($page->error)) {
        $page->success = "The installation was successful ! Thank you for using master loging system and we hope you enjo it ! Have fun ! <br/><br/>
                          <a class='btn btn-success' href='./index.php'>Start exploring</a>
                          <br/><br/>
                          <h3>USER: admin <br/> PASSWORD: 1234</h3>";
    }
}
?><!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Installer</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">

        <link rel="stylesheet" href="./css/bootstrap.min.css">
        <link rel="stylesheet" href="./css/main.css">

        <style>
            body {
                padding-top: 70px;
                padding-bottom: 40px;
            }
        </style>
        <script src="./js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>        

    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
        <![endif]--> 
        <div class="bg-dark fixed-top">
            <div class="container">
                <nav class="navbar navbar-dark bg-dark">
                    <a class="navbar-brand" href="#">
                        <img src="./img/system.svg" width="32" height="32" class="d-inline-block align-top" alt="ico">
                        Master Login System
                    </a>
                </nav>                
            </div>
        </div>
        <div class="container">
            <?php
            if (isset($page->error)) {
                $options->error($page->error);
            } else if (isset($page->success)) {
                $options->success($page->success);
                exit;
            }
            ?>
            <form class="form-horizontal well span6" action="?" method="post">

                <h3>Install form</h3>
                <br/>

                <div class="row">
                    <div class="col-6">
                        <fieldset class='scheduler-border'>
                            <legend class='scheduler-border'>Site Info</legend>
                            <div class="form-group">
                                <label for="sitename">Site Name</label>                    
                                <input id="sitename" name="sitename" type="text" class="form-control" value="Demo Site" style="max-width: 250px;">
                                <small id="sitenameHelpBlock" class="form-text text-muted">The name of the site will be used in the top left corner.</small>

                            </div>
                            <div class="form-group">
                                <label for="siteurl">Site Url</label>
                                <input id="siteurl" name="siteurl" type="text" class="form-control" value="http://<?php echo $_SERVER['SERVER_NAME'] . dirname($_SERVER['REQUEST_URI']); ?>" style="max-width: 350px;">
                                <small id="siteurlHelpBlock" class="form-text text-muted">The url of your site(no end /).</small>
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="sitedomain">Domain</label>
                                <input id="sitedomain" name="sitedomain" type="text" class="form-control" value="" style="max-width: 150px;">
                                <small id="sitedomainHelpBlock" class="form-text text-muted">The domain of your site(no http:// and www ).</small>
                            </div>
                            <div class='form-row'>
                                <div class="col-md-6">
                                    <label class="control-label" for="adminemail">Admin Email</label>
                                    <input id="adminemail" name="adminemail" type="text" class="form-control" value="">
                                    <small id="adminemailHelpBlock" class="form-text text-muted">Enter the Admin Email.</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="control-label" for="supportemail">Support Email</label>
                                    <input id="supportemail" name="supportemail" type="text" class="form-control" value="" >
                                    <small id="supportemailHelpBlock" class="form-text text-muted">Enter the support Email.</small>
                                </div>
                            </div>    
                        </fieldset>
                    </div>
                    <div class="col-3">
                        <fieldset class='scheduler-border'>
                            <legend class='scheduler-border'>Data Base Info</legend>
                            <div class="form-group">
                                <label class="control-label" for="dbhost">Database Host</label>
                                <input id="dbhost" name="dbhost" type="text" class="form-control" value="localhost" style="max-width: 150px;">                  
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="dbuser">Database Username</label>
                                <input id="dbuser" name="dbuser" type="text" class="form-control" value="root" style="max-width: 150px;">
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="dbpass">Database Password</label>
                                <input id="dbpass" name="dbpass" type="password" class="form-control" value="" style="max-width: 150px;">
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="dbname">Database Name</label>
                                <input id="dbname" name="dbname" type="text" class="form-control" value="mls" style="max-width: 150px;">
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="tbprefix">Tables Prefix</label>
                                <input id="tbprefix" name="tbprefix" type="text" class="form-control" value="mls_" style="max-width: 150px;">
                            </div>
                            <br>
                            <br>                
                        </fieldset>
                    </div>
                    <div class="col-3">
                        <fieldset class='scheduler-border'>
                            <legend class='scheduler-border'>Settings Info</legend>
                            <div class="form-group">
                                <label class="control-label" for="dbhost">Database Host</label>
                                <input id="dbhost" name="dbhost" type="text" class="form-control" value="localhost" style="max-width: 150px;">                  
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="dbuser">Database Username</label>
                                <input id="dbuser" name="dbuser" type="text" class="form-control" value="root" style="max-width: 150px;">
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="dbpass">Database Password</label>
                                <input id="dbpass" name="dbpass" type="password" class="form-control" value="" style="max-width: 150px;">
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="dbname">Database Name</label>
                                <input id="dbname" name="dbname" type="text" class="form-control" value="mls" style="max-width: 150px;">
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="tbprefix">Tables Prefix</label>
                                <input id="tbprefix" name="tbprefix" type="text" class="form-control" value="mls_" style="max-width: 150px;">
                            </div>
                        </fieldset>
                    </div>
                </div>
                <br/>
                <input type='submit' value='Install' class='btn btn-success'>
            </form>
        </div> <!-- /container -->       

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="./js/vendor/jquery-1.9.1.min.js"><\/script>')</script>

        <script src="./js/vendor/bootstrap.min.js"></script>

    </body>
</html>
