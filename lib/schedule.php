<?php

if(!defined('__ROOT__'))
  define('__ROOT__', realpath(dirname(dirname(__FILE__))));

require_once __ROOT__ . "/lib/interactive.php";
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
        "#talks" => $default["#talks"],
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
    "#talks" => $exc["#talks"],
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

function set_speaker($snt, &$queue){
  $snt["who"] = pop_and_push($queue, $snt["#talks"]);
  return $snt;
}

function set_speakers($snts){
  $queue = read_queue(__ROOT__ . "/conf/queue");

  $iter = function($snt) use(&$queue){ return set_speaker($snt, $queue); };
  $snts = array_map($iter, $snts);
  return $snts;
}

/* Return following 10 seminars from tomorrow */
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

/* Return the seminars on n days later

   CUATION: n should be bigger than 0

   CAUTION: The return type is array of seminars, not a sminar,
   because there may be more than one seminar in a day, by any chance.
   */
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

/* Ask to select a seminar in command line */
function select_snt(){
  $snts = get_schedule();
  foreach($snts as $key => $snt){
    echo ("$key) " . date('Y-m-d H:i', time_of_when($snt["when"])) . "\n");
  }
  echo "\n";
  echo "Select a seminar (x to exit): ";
  $input_str = my_fgets();
  echo "\n";

  if($input_str === "x"){
    echo "Exit\n";
    exit(0);
  }else if(my_key_exists($input_str, $snts)){
    $snt = $snts[(int)$input_str];
    echo ( "The seminar on "
         . date('Y-m-d H:i', time_of_when($snt["when"]))
         . " is selected.\n\n" );
    return $snt;
  }else{
    echo "Your input is invalid.\n";
    exit(1);
  }
}

?>
