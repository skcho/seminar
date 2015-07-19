<?php

function read_queue($filename){
  $str = file_get_contents($filename);
  if($str === false){
    my_log(__FILE__, "$filename cannot be read\n");
    exit(1);
  }
  $ids = explode("\n", $str);
  $ids = array_filter($ids,
                      function($id){
                        if($id === "") return false;
                        else return true;
                      });
  return $ids;
}

function write_queue($filename, $queue){
  if(file_put_contents($filename, implode("\n", $queue)) === false){
    my_log(__FILE__, "$filename cannot be written\n");
    exit(1);
  }
  my_log(__FILE__, "$filename updated\n");
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