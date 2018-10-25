<?php
function lang($phrase){
    static $lang = array(
        'home' => 'الرئيسية',
        'admin_panel' => 'لوحة التحكم',
        'site_name' => 'ستور إيجبت',
        'cats' =>'Categories',
        'items' => 'Items',
        'users'=>'members',
        'logs'=>'Logs',
        'statistic' => 'Statistic'
    );
    return $lang[$phrase];
}