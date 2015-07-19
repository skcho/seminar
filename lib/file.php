<?php

if(!defined('__ROOT__'))
  define('__ROOT__', realpath(dirname(dirname(__FILE__))));

require_once __ROOT__ . "/lib/log.php";


function my_file_get_contents($filename){
  $str = file_get_contents($filename);
  if($str === false){
    my_log(__FILE__, "$filename cannot be read\n");
    exit(1);
  }
  return $str;
}

function my_file_put_contents($filename, $data){
  if(file_put_contents($filename, $data) === false){
    my_log(__FILE__, "$filename cannot be written\n");
    exit(1);
  }else{
    my_log(__FILE__, "$filename updated\n");
  }
}

function json_get_contents($filename){
  return json_decode(my_file_get_contents($filename), true);
}

function json_put_contents($filename, $arr){
  my_file_put_contents($filename, json_encode($arr));
}

?>
