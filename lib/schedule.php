<?php

if(!defined('__ROOT__'))
  define('__ROOT__', realpath(dirname(dirname(__FILE__))));

require_once __ROOT__ . "/lib/log.php";
require_once __ROOT__ . "/lib/queue.php";


function next_day($d){
  return date('Y-m-d', strtotime('+1 day', strtotime($d)));
}

function is_day($d, $day){
  if(date('l', strtotime($d)) === $day) return true;
  else return false;
}

function get_default_snts($default, $n){
  $snts = array();
  $d = date('Y-m-d');
  while($n > 0){
    $d = next_day($d);
    if(is_day($d, $default["when"]["day"])){
      $snt = array(
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
      array_push($snts, $snt);
      $n = $n - 1;
    }
  }
  return $snts;
}

function add_snt($snts, $exc){
  $snt = array(
    "when" => $exc["when"],
    "where" => $exc["where"],
    "who" => $exc["who"],
  );
  array_push($snts, $snt);
  return $snts;
}

function remove_snt($snts, $exc){
  $snts = array_filter($snts,
                        function($snt) use($exc){
                          if($snt["when"] === $exc["when"]) return false;
                          else return true;
                        });
  return $snts;
}

function modify_snt($snts, $exc){
  $snts = remove_snt($snts, array("when" => $exc["from"]));
  $snts = add_snt($snts, $exc);
  return $snts;
}

function apply_exception($snts, $excs){
  foreach($excs as $exc){
    if($exc["mode"] === "add"){
      $snts = add_snt($snts, $exc);
    }else if($exc["mode"] === "remove"){
      $snts = remove_snt($snts, $exc);
    }else if($exc["mode"] === "modify"){
      $snts = modify_snt($snts, $exc);
    }else{
      my_log(__FILE__, "Invalid mode name in exception\n");
      exit(1);
    }
  }
  sort($snts);
  return $snts;
}

function is_after_today($snt){
  $tomorrow = strtotime('+1 day');
  $snt_day = strtotime( $snt["when"]["year"] . "-" . $snt["when"]["month"]
                       . "-" . $snt["when"]["day"] );
  if($snt_day >= $tomorrow) return true;
  else return false;
}

function set_speaker($snt, &$queue_all, &$queue_ropas, &$queue_sf){
  $speaker = array();
  foreach($snt["who"] as $grp => $num){
    if($grp === "all"){
      $speaker = array_merge($speaker, pop_and_push($queue_all, $num));
    }else if($grp === "ropas"){
      $speaker = array_merge($speaker, pop_and_push($queue_ropas, $num));
    }else if($grp === "sf"){
      $speaker = array_merge($speaker, pop_and_push($queue_sf, $num));
    }else{
      my_log(__FILE__, "Invalid group name\n");
      exit(1);
    }
  }

  $snt["who"] = $speaker;
  return $snt;
}

function set_speakers($snts){
  $queue_all = read_queue(__ROOT__ . "/conf/queue.all");
  $queue_ropas = read_queue(__ROOT__ . "/conf/queue.ropas");
  $queue_sf = read_queue(__ROOT__ . "/conf/queue.sf");

  $iter = function($snt) use(&$queue_all, &$queue_ropas, &$queue_sf){
    return set_speaker($snt, $queue_all, $queue_ropas, $queue_sf);
  };
  $snts = array_map($iter, $snts);
  return $snts;
}

/* Return following 10 S&Ts from tomorrow */
function get_schedule(){
  $conf = json_decode(file_get_contents(__ROOT__ . "/conf/info.json"), true);
  $snts = get_default_snts($conf["default"], 30);
  $snts = apply_exception($snts, $conf["exception"]);
  $snts = array_filter($snts, "is_after_today");
  $snts = array_slice($snts, 0, 10);
  $snts = set_speakers($snts);
  return $snts;
}

?>
