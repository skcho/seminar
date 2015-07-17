<?php

function replace($filename, $src){
  $msg = file_get_contents($filename);
  foreach($src as $key => $value)
    $msg = str_replace("{{" . $key . "}}", $value, $msg);
  return $msg;
}

?>