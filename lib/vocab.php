<?php

/* Check whether an array has a key */
function my_key_exists($input_str, $arr){
  $keys = array_map(function($key){return (string)$key;}, array_keys($arr));
  foreach($keys as $key){ if($input_str === $key) return true; }
  return false;
}

/* Read a line without line space */
function my_fgets(){ return str_replace("\n", "", fgets(STDIN)); }

/* Convert an array of time to a time of php */
function time_of_when($when){
  return mktime($when["hour"], $when["min"], 0,
                $when["month"], $when["day"], $when["year"]);
}
