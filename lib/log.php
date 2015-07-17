<?php

if(!defined('__ROOT__'))
  define('__ROOT__', realpath(dirname(dirname(__FILE__))));


/* Log messages

   The first argument, $php_file, is a php file name (or path) that
   invokes the my_log function.

   e.g.) my_log(__FILE__, "Cannot open file\n");
 */
function my_log($php_file, $msg){
  $log_file = date('ymd_His_') . basename($php_file, ".php");
  file_put_contents(__ROOT__ . "/log/$log_file", $msg)
    or die("Unable to open log file!");
}

?>
