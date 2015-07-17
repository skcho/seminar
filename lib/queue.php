<?php

function read_queue($filename){
  $ids = explode("\n", file_get_contents($filename));
  $ids = array_filter($ids,
                      function($id){
                        if($id === "") return false;
                        else return true;
                      });
  return $ids;
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