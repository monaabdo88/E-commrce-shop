<?php
//Route
include "connect.php";
$tpl = "includes/templates/"; // Template Directory
$langDir = "includes/languages/";
$func = "includes/functions/";
$css = "layout/css/"; //css files Directory
$js = "layout/js/"; //js files Directory
include $func."functions.php";
include $langDir."english.php";
include $tpl.'header.php';
if(!isset($noNavbar)) {
    include $tpl . 'navbar.php';
}