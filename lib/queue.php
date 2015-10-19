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
  if(!my_file_put_contents($filename, implode("\n", $queue))) exit(1);
}

function pop_and_push(&$queue, $num, $except = array()){
  $heads = array();
  $elts = array();
  for($i = 0; $i < $num; $i++){
    $elt = array_shift($queue);
    if(in_array($elt, $except)){
      $i--;
      array_push($heads, $elt);
      continue;
    }
    array_push($queue, $elt);
    array_push($elts, $elt);
  }
  $queue = array_merge($heads, $queue);

  return $elts;
}

?>
