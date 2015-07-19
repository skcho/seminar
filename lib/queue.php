<?php

if(!defined('__ROOT__'))
  define('__ROOT__', realpath(dirname(dirname(__FILE__))));

require_once __ROOT__ . "/lib/file.php";


function read_queue($filename){
  $ids = explode("\n", my_file_get_contents($filename));
  $ids = array_filter($ids,
                      function($id){
                        if($id === "") return false;
                        else return true;
                      });
  return $ids;
}

function write_queue($filename, $queue){
  my_file_put_contents($filename, implode("\n", $queue));
}

function pop_and_push(&$queue, $num){
  $elts = array();
  for($i = 0; $i < $num; $i++){
    $elt = array_shift($queue);
    array_push($queue, $elt);
    array_push($elts, $elt);
  }
  return $elts;
}

?>