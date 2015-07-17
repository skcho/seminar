<?php

if(!defined('__ROOT__'))
  define('__ROOT__', realpath(dirname(dirname(__FILE__))));

require_once __ROOT__ . "/lib/log.php";


function next_day($d){
  return date('Y-m-d', strtotime('+1 day', strtotime($d)));
}

function is_day($d, $day){
  if(date('l', strtotime($d)) === $day) return true;
  else return false;
}

function get_datault_talks($default, $n){
  $talks = array();
  $d = date('Y-m-d');
  while($n > 0){
    $d = next_day($d);
    if(is_day($d, $default["when"]["day"])){
      $talk = array(
        "when" => array(
          "year" => (int)date('Y', strtotime($d)),
          "month" => (int)date('m', strtotime($d)),
          "day" => (int)date('d', strtotime($d)),
          "hour" => $default["when"]["hour"],
          "min" => $default["when"]["min"],
        ),
        "where" => $default["where"],
        "who" => $default["who"],
      );
      array_push($talks, $talk);
      $n = $n - 1;
    }
  }
  return $talks;
}

function add_talk($talks, $exc){
  $talk = array(
    "when" => $exc["when"],
    "where" => $exc["where"],
    "who" => $exc["who"],
  );
  array_push($talks, $talk);
  return $talks;
}

function remove_talk($talks, $exc){
  $talks = array_filter($talks,
                        function($talk) use($exc){
                          if($talk["when"] === $exc["when"]) return false;
                          else return true;
                        });
  return $talks;
}

function modify_talk($talks, $exc){
  $talks = remove_talk($talks, array("when" => $exc["from"]));
  $talks = add_talk($talks, $exc);
  return $talks;
}

function apply_exception($talks, $excs){
  foreach ($excs as $exc){
    if($exc["mode"] === "add"){
      $talks = add_talk($talks, $exc);
    }else if($exc["mode"] === "remove"){
      $talks = remove_talk($talks, $exc);
    }else if($exc["mode"] === "modify"){
      $talks = modify_talk($talks, $exc);
    }else{
      my_log(__FILE__, "Invalid mode name in exception\n");
      exit(1);
    }
  }
  sort($talks);
  return $talks;
}

function is_after_today($talk){
  $tomorrow = strtotime('+1 day');
  $talk_day = strtotime( $talk["when"]["year"] . "-" . $talk["when"]["month"]
                       . "-" . $talk["when"]["day"] );
  if($talk_day >= $tomorrow) return true;
  else return false;
}

/* Return 10 following talks after today */
function get_schedule(){
  $conf = json_decode(file_get_contents(__ROOT__ . "/conf/info.json"), true);
  $talks = get_datault_talks($conf["default"], 30);
  $talks = apply_exception($talks, $conf["exception"]);
  $talks = array_filter($talks, "is_after_today");
  $talks = array_slice($talks, 0, 10);
  /* TODO: replace "who" part */

  return $talks;
}

?>
