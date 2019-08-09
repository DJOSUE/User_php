<?php
/*
 * MASTER LOGIN SYSTEM
 * @author David Huaraca
 * May 2019
 */

if (!isset($_SESSION['token'])) {
    $_SESSION['token'] = sha1(rand());
} // random token

echo "
<!-- Modal -->
<div class='modal fade' id='loginModal' tabindex='-1' role='dialog' aria-hidden='true' >
    <div class='modal-dialog modal-dialog-centered' role='document'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h5 class='modal-title' id='exampleModalCenterTitle'>Login</h5>
                <button type='button' class='close' data-dismiss='modal' aria-label='Close'>&times;</button>                    
            </div>
            <div class='modal-body' style='text-align:center;'>
                <div class='row-fluid'>
                    <div class='span10 offset1'>
                        <div id='modalTab'>
                            <div class='tab-content'>
                                <div class='tab-pane active' id='login'>
                                    <form method='post' action='$set->url/login.php' name='login_form'>
                                        <p><input type='text' class='span12' name='name' placeholder='Username'></p>
                                        <p><input type='password' class='span12' name='password' placeholder='Password'></p>
                                        <p>
                                                <input class='pull-left' type='checkbox' name='r' value='1' id='rm'>  
                                                <label class='pull-left' for='rm'>Remember Me</label>
                                        </p>
                                        <div class='clearfix'></div>

                                        <input type='hidden' name='token' value='" . $_SESSION['token'] . "'>

                                        <p><button type='submit' class='btn btn-primary'>Sign in</button>
                                        <a href='#forgotpassword' data-toggle='tab'>Forgot Password?</a>
                                        </p>
                                    </form>
                                </div>
                                <div class='tab-pane fade' id='forgotpassword'>
                                    <form method='post' action='$set->url/login.php?forget=1' name='forgot_password'>
                                        <p>Hey this stuff happens, send us your email and we'll reset it for you!</p>
                                        <input type='text' class='span12' name='email' placeholder='Email'>
                                        <br/>
                                        <br/>
                                        <button type='submit' class='btn btn-primary'>Submit</button>
                                        <br/>
                                        <a href='#login' data-toggle='tab'>Wait, I remember it now!</a>
                                        <input type='hidden' name='token' value='" . $_SESSION['token'] . "'>
                                        </p>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
";
?>
<div class="container">	
    <br/>
    <hr>
    <div class="row-fluid">
        <div class="span12">
            <div class="span8">
                <a href="#">Terms of Service</a>    
                <a href="#">Privacy</a>    
                <a href="#">Security</a>
            </div>
            <div class="span4">
                <p class="muted pull-right">© 2013 Company Name. All rights reserved</p>
            </div>
        </div>
    </div>
</div>    

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="<?php echo $set->url; ?>/js/vendor/jquery-1.9.1.min.js"><\/script>')</script>

<script src="<?php echo $set->url; ?>/js/vendor/bootstrap.min.js"></script>

<!-- Validate Plugin -->
<script src="<?php echo $set->url; ?>/js/vendor/jquery.validate.min.js"></script>

<script src="<?php echo $set->url; ?>/js/main.js"></script>

<script>

</script>
</body>
</html>