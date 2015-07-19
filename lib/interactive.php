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

/* Ask yes or no */
function ask_y_or_n($question){
  echo "$question (y or n): ";
  $input_str = my_fgets();
  echo "\n";

  if($input_str === "y") return true;
  else if($input_str === "n") return false;
  else{
    echo "Your input is invalid.\n";
    exit(1);
  }
}

function echo_msg($msg){
  echo "[MESSAGE START]\n";
  echo $msg;
  echo "[MESSAGE END]\n\n";
}

?>
