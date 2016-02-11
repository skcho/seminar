<?php

if(!defined('__ROOT__'))
  define('__ROOT__', realpath(dirname(dirname(__FILE__))));

require_once __ROOT__ . "/lib/vocab.php";

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
