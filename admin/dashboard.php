<?php
session_start();
if(!isset($_SESSION['username'])){
    header('Location: index.php');
    exit();
}
$pageTitle = "Dashboard";
include "init.php";
?>
<?php include $tpl."footer.php"?>
