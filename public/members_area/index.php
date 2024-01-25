<?php
include('config.php')
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link href="<?php echo $design; ?>/style.css" rel="stylesheet" title="Style" />
        <title>Members Area</title>
    </head>
    <body>
    	<div class="header">
        	<a href="<?php echo $url_home; ?>"><img src="<?php echo $design; ?>/images/logo.png" alt="Members Area" /></a>
	    </div>
        <div class="content">
<?php
//We display a welcome message, if the user is logged, we display it username
?>
Hello<?php if(isset($_SESSION['username'])){echo ' '.htmlentities($_SESSION['username'], ENT_QUOTES, 'UTF-8');} ?>,<br />
Welcome on our website.<br />
You can <a href="users.php">see the list of users</a>.<br /><br />
<?php
//If the user is logged, we display links to edit his infos, to see his pms and to log out
if(isset($_SESSION['username']))
{
?>
<a href="edit_infos.php">Edit my personnal informations</a><br />
<a href="connexion.php">Logout</a>
<?php
}
else
{
//Otherwise, we display a link to log in and to Sign up
?>
<a href="sign_up.php">Sign up</a><br />
<a href="connexion.php">Log in</a>
<?php
}
?>
		</div>
		<div class="foot"><a href="http://www.webestools.com/">Webestools</a></div>
	</body>
</html>