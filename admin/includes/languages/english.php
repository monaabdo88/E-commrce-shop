<?php
function lang($phrase){
    static $lang = array(
        'home' => 'Home',
        'admin_panel' => 'Admin Panel',
        'site_name' => 'Store Egypt',
        'cats' =>'Categories',
        'items' => 'Items',
        'users'=>'members',
        'logs'=>'Logs',
        'statistic' => 'Statistic'
    );
    return $lang[$phrase];
}