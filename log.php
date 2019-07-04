<?php

defined("TAB1") or define("TAB1", "\t");

if(!empty($_POST) && !empty($_POST['platform_data'])) {
    $platform_data = $_POST['platform_data'];
    $platform_array = json_decode($platform_data, true);
}

$today = date("Y-m-d");

$hour = date("H");

$datetime = (new DateTime('now'))->format('Y-m-d H:i:s');

if($hour <= 12) {
    $suffix = '-AM';
} else {
    $suffix = '-PM';
}

$log_file = 'logs/' . $today . $suffix . '.csv'; //I'll create a csv file per 12h

$mode = (!file_exists($log_file)) ? 'w':'a';

$handle = fopen($log_file, $mode) or die('Cannot open file:  ' . $log_file);

$first_line = '';
$row = '';


if ($mode === "w") {
    $first_line .= '"DATETIME"' . TAB1;
}
$row .= '"' . $datetime . '"' . TAB1;


foreach($_SERVER as $k => $v) {
    if ($mode === "w") {
       $first_line .= '"' . $k . '"' . TAB1;
    }
    $row .= '"' . $v . '"' . TAB1;
}

foreach($platform_array as $k => $v) {

    if(!is_array($v)) {

        if ($mode === "w") {
            $first_line .= strtoupper('"' . $k . '"' . TAB1);
        }

        $row .= '"' . $v . '"' . TAB1;

    } else {
        foreach ($v as $kk => $vv) {

            if ($mode === "w") {
                $first_line .= strtoupper('"' . $k . '_' . $kk . '"' . TAB1);
            }

            $row .= '"' . $vv . '"' . TAB1;
        }
    }
}

if($geo = file_get_contents('http://www.geoplugin.net/php.gp?ip='. $_SERVER['REMOTE_ADDR'])) {
    if(!empty($geo)) {

        $geo_un = unserialize($geo);

        foreach ($geo_un as $k => $v) {
            if ($mode === "w") {
                $first_line .= strtoupper('"' . $k . '"' . TAB1);
            }
            $row .= '"' . $v . '"' . TAB1;
        }
    }
}


if(!empty($first_line)) {
    fwrite($handle, $first_line . PHP_EOL);
}

fwrite($handle, $row . PHP_EOL);

fclose($handle);