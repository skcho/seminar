<?php

if(!defined('__ROOT__'))
  define('__ROOT__', realpath(dirname(dirname(__FILE__))));


/* Log messages

   The first argument, $php_file, is a php file name (or path) that
   invokes the my_log function.

   e.g.) my_log(__FILE__, "Cannot open file\n");
 */
function my_log($php_file, $message){
  $log_file = date('ymd_His_') . basename($php_file, ".php");
  $f = fopen(__ROOT__ . "/log/$log_file", "w") or die("Unable to open log file!");
  fwrite($f, $message);
  fclose($f);
}

?>
