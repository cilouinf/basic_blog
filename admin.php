<?php
session_start();
require_once 'classes/Session.php';
require_once 'includes/header.inc.php';

if(isset($_GET['logout'])) {
    require_once 'app/logout.php';
}

if(!Session::exists('admin') && !Session::exists('member')) {
    require_once 'forms/authenticate.php'; // if the user is not authenticated yet
} else {
    header('Location: index.php');
    exit();
}

require_once 'includes/footer.inc.php';