<?php

if(!defined('__ROOT__'))
  define('__ROOT__', realpath(dirname(dirname(__FILE__))));

require_once __ROOT__ . "/lib/vocab.php";
require_once __ROOT__ . "/lib/log.php";
require_once __ROOT__ . "/lib/queue.php";
require_once __ROOT__ . "/lib/read_data.php";


function get_default_snts($n){
  $default = get_default_conf();
  $snts = array();
  $d = time();
  while($n > 0){
    if(date('l', $d) === $default["when"]["day"]){
      $snt = array(
        "when" => array(
          "year" => (int)date('Y', $d),
          "month" => (int)date('m', $d),
          "day" => (int)date('d', $d),
          "hour" => $default["when"]["hour"],
          "min" => $default["when"]["min"],
        ),
        "where" => $default["where"],
        "who_grp" => $default["who_grp"],
      );
      array_push($snts, $snt);
      $n = $n - 1;
    }
    $d = strtotime('+1 day', $d);
  }
  return $snts;
}

function add_snt($snts, $exc){
  $snt = array(
    "when" => $exc["when"],
    "where" => $exc["where"],
    "who_grp" => $exc["who_grp"],
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

function apply_exception($snts){
  foreach(get_exception_conf() as $exc){
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

function is_after($when, $t){
  $snt_day = time_of_when($when);
  if($snt_day >= $t) return true;
  else return false;
}

function set_speaker($snt, &$queue_all, &$queue_ropas, &$queue_sf, &$queue_chair){
  $speaker = array();
  foreach($snt["who_grp"] as $grp => $num){
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

  $chair = pop_and_push($queue_chair, 1, $speaker);

  $snt["who"] = $speaker;
  $snt["chair"] = $chair[0];
  return $snt;
}

function set_speakers($snts){
  $queue_all = read_queue(__ROOT__ . "/conf/queue.all");
  $queue_ropas = read_queue(__ROOT__ . "/conf/queue.ropas");
  $queue_sf = read_queue(__ROOT__ . "/conf/queue.sf");
  $queue_chair = read_queue(__ROOT__ . "/conf/queue.chair");

  $iter =
    function($snt) use(&$queue_all, &$queue_ropas, &$queue_sf, &$queue_chair){
    return set_speaker($snt, $queue_all, $queue_ropas, $queue_sf, $queue_chair);
  };
  $snts = array_map($iter, $snts);
  return $snts;
}

/* Return following 10 S&Ts from tomorrow */
function get_schedule(){
  $t = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y'));
  $snts = get_default_snts(30);
  $snts = apply_exception($snts);
  $snts = array_filter($snts,
                       function($snt) use($t){
                         return is_after($snt["when"], $t);
                       });
  $snts = array_slice($snts, 0, 10);
  $snts = set_speakers($snts);
  return $snts;
}

/* Return the S&T on n days later

   CUATION: n should be bigger than 0

   CAUTION: The return type is array of S&T, not an S&T, because there
   may be more than one S&T in a day, by any chance.  */
function snts_n_days_later($n){
  if($n <= 0){
    my_log(__FILE__, "n should be bigger than 0\n");
    exit(1);
  }
  $is_n_days_later = function($snt) use($n){
    $n_days_later = strtotime("+$n day");
    if( (int)date('Y', $n_days_later) === $snt["when"]["year"]
      && (int)date('m', $n_days_later) === $snt["when"]["month"]
      && (int)date('d', $n_days_later) === $snt["when"]["day"])
        return true;
    else return false;
  };
  return array_filter(get_schedule(), $is_n_days_later);
}

function snts_today(){
  $is_today = function($snt){
    if( (int)date('Y') === $snt["when"]["year"]
      && (int)date('m') === $snt["when"]["month"]
      && (int)date('d') === $snt["when"]["day"] )
        return true;
    else false;
  };
  $snts = get_default_snts(30);
  $snts = apply_exception($snts);
  $snts = array_filter($snts, $is_today);
  return $snts;
}

/* Ask to select a S&T in command line */
function select_snt(){
  $snts = get_schedule();
  foreach($snts as $key => $snt){
    echo ("$key) " . date('Y-m-d H:i', time_of_when($snt["when"])) . "\n");
  }
  echo "\n";
  echo "Select a S&T (x to exit): ";
  $input_str = my_fgets();
  echo "\n";

  if($input_str === "x"){
    echo "Exit\n";
    exit(0);
  }else if(my_key_exists($input_str, $snts)){
    $snt = $snts[(int)$input_str];
    echo ( "The S&T on "
         . date('Y-m-d H:i', time_of_when($snt["when"]))
         . " is selected.\n\n" );
    return $snt;
  }else{
    echo "Your input is invalid.\n";
    exit(1);
  }
}
