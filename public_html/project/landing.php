<?php
require(__DIR__ . "/../../partials/nav.php");
error_log("Session: " . var_export($_SESSION, true));
?>
<center>
<h1 class = "landing">Landing Page</h1>

<?php if(is_logged_in(true)):?>
    
    <p class = "welcome" >Welcome, <?php echo get_username() ?>!</p>
</center>
<?php endif;?>

<?php
require(__DIR__."/../../partials/flash.php");
?>