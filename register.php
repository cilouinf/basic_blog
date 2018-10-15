<?php
session_start();
require_once 'classes/Session.php';
require_once  'classes/Token.php';

if(Session::exists('login') && !Session::exists('admin')) {
    header('Location: index.php');
    exit();
}

require_once 'includes/header.inc.php';
require_once  'forms/new_user.php';
require_once 'includes/footer.inc.php';

?>



