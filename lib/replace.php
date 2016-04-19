<?php

if(!defined('__ROOT__'))
  define('__ROOT__', realpath(dirname(dirname(__FILE__))));

require_once __ROOT__ . "/lib/file.php";


function replace($filename, $src){
  $msg = my_file_get_contents($filename);
  foreach($src as $key => $value)
    $msg = str_replace("{{" . $key . "}}", $value, $msg);
  return $msg;
}
