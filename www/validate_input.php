<?php

if(!defined('__ROOT__'))
  define('__ROOT__', realpath(dirname(dirname(__FILE__))));

require_once __ROOT__ . "/lib/read_data.php";

function get_valid_id($id){
    get_member($id);
    return $id;
}

function get_valid_date($date){
    $date_array = explode('-', $date);
    if(checkdate($date_array[1], $date_array[2], $date_array[0])){
	$date_str = $date_array[0] . '-' . $date_array[1] . '-' . $date_array[2];
	return date('Y-m-d', strtotime($date_str));
    }
    exit(1);
}

function get_valid_time($time){
    if(is_null($time)) exit(1);
    return $time;
}
