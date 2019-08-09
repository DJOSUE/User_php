<?php
/*
 * MASTER LOGIN SYSTEM
 * @author David Huaraca
 * May 2019
 */

// we generate the navbar components in case they weren't before
if ($page->navbar == array()) {
    $page->navbar = $presets->GenerateNavbar();
}

if (!$user->islg()) { // if it's not logged in we hide the user menu
    unset($page->navbar[count($page->navbar) - 1]);
}

?><!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title><?php echo $page->title; ?></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">

        <link rel="stylesheet" href="<?php echo $set->url; ?>/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo $set->url; ?>/css/main.css">
        <link rel="stylesheet" href="<?php echo $set->url; ?>/js/jquery.bootgrid.css"> 
        
        <style>
            body {
                padding-top: 80px;
                padding-bottom: 40px;
            }
        </style>
        <script src="<?php echo $set->url; ?>/js/jquery.bootgrid.js"></script>
        
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
        <![endif]-->         

        <div class="bg-light fixed-top">             
            <div class="container">
                <nav class="navbar navbar-expand-lg navbar-light  bg-light ">
                <a class="brand" href="<?php echo $set->url; ?>">
                    <img src="<?php echo $set->url; ?>/img/system.svg" alt="Logo" class="img-fluid" style="margin-right: 10px;">
                    <?php echo $set->site_name; ?>
                </a>    
                <button class="navbar-toggler" 
                        type="button" 
                        data-toggle="collapse" 
                        data-target="#navbar" 
                        aria-controls="navbarNav" 
                        aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>                
                <div class="collapse navbar-collapse" id="navbar">
                    <ul class="navbar-nav ml-auto">
<?php
// we generate a simple menu this may need to be adjusted depending on your needs
// but it should be ok for most common items
foreach ($page->navbar as $key => $v) {

    if ($v[0] == 'item') {
    
        echo "          <li".($v[1]['class'] ? " class='".$v[1]['class']."'" : "").">
                            <a class='nav-link' href='".$v[1]['href']."'>".$v[1]['name']."</a>
                        </li>";
    
    } else if($v[0] == 'dropdown') {
        
        echo "
                        <li class='nav-item dropdown'>"."
                            <a id=\"DropDownList\"".
                        // extra classes 
                            ($v['class'] ? " class='".$v['class'] : "")."'".
                        // extra data-toggle
                            ($v['data-toggle'] ? " data-toggle='".$v['data-toggle']."'" : "").
                        // extra aria-haspopup
                            ($v['aria-haspopup'] ? " aria-haspopup='".$v['aria-haspopup']."'" : "").
                        // extra aria-expanded
                            ($v['aria-expanded'] ? " aria-expanded='".$v['aria-expanded']."'" : "").                
                        ">".$v['name']."</a>
                            <div class='dropdown-menu'".
                                "aria-labelledby='DropDownList'>";
        
        foreach ($v[1] as $k => $v) {
            echo "              <a ".($v['class'] ? " class='" . $v['class'] . "'" : "") . "
                                   href=\"" . $v['href'] . "\">" . $v['name'] . "</a>";
        }
        echo "             </div>"
        . "             </li>";
    }    
}

echo "              </ul>";

if(!$user->islg()) { 

echo "<span class='justify-content-end'>
        <a href='$set->url/register.php' class='btn btn-primary btn-small'>Sign Up</a>
        <!-- <a href='$set->url/login.php' class='btn btn-small'>Login</a> -->
        <a href='#loginModal' data-toggle='modal' class='btn btn-small'>Login</a>
    </span>
    ";
}
echo "

                </div><!--/.nav-collapse -->    
                </nav>
            </div><!-- container -->
        </div><!--/.bg-dark fixed-top --> ";

if($user->islg() && $set->email_validation && ($user->data->validated != 1)) {
    $options->fError("Your account is not yet acctivated ! Please check your email !");
}

if(file_exists('install.php')) {
    $options->fError("You have to delete the install.php file before you start using this app.");
}

if(isset($_SESSION['success'])){
    $options->success($_SESSION['success']);
    unset($_SESSION['success']);
}
if(isset($_SESSION['error'])){
    $options->error($_SESSION['error']);
    unset($_SESSION['error']);

}
flush(); // we flush the content so the browser can start the download of css/js
