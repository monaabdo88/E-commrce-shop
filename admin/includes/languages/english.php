<?php
function lang($phrase){
    static $lang = array(
        'home' => 'Home',
        'admin' => 'Admin Panel',
        'site_name' => 'E-commerce'
    );
    return $lang[$phrase];
}