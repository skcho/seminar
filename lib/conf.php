<?php

if(!defined('__ROOT__'))
  define('__ROOT__', realpath(dirname(dirname(__FILE__))));

function get_conf(){
  $conf = json_decode(file_get_contents(__ROOT__ . "/conf/info.json"), true);
  return $conf;
}

function get_default_conf(){
  $conf = get_conf();
  return $conf["default"];
}

function get_exception_conf(){
  $conf = get_conf();
  return $conf["exception"];
}

function get_email_conf(){
  $conf = get_conf();
  return $conf["email"];
}

?>
