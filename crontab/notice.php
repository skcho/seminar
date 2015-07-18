<?php

/* Send notice mail

   1. If it runs automatically,

   > php notice.php fst
   > php notice.php snd

   2. If it runs manually,

   > php notice.php
 */

if(!defined('__ROOT__'))
  define('__ROOT__', realpath(dirname(dirname(__FILE__))));

require_once __ROOT__ . "/lib/schedule.php";
require_once __ROOT__ . "/lib/log.php";
require_once __ROOT__ . "/lib/replace.php";
require_once __ROOT__ . "/lib/member.php";
require_once __ROOT__ . "/lib/send_mail.php";


function my_key_exists($input_str, $arr){
  $keys = array_map(function($key){return (string)$key;}, array_keys($arr));
  foreach($keys as $key){ if($input_str === $key) return true; }
  return false;
}

function my_fgets(){ return str_replace("\n", "", fgets(STDIN)); }

function time_of_when($when){
  return strtotime( $when["year"] . "-"
                  . $when["month"] . "-"
                  . $when["day"] . "T"
                  . $when["hour"] . ":"
                  . $when["min"] . ":" . "0" );
}

function gen_talk_msg($is_fst, $t, $where, $who){
  $date = date('ymd', $t);
  $contents_json = file_get_contents(__ROOT__ . "/data/{$date}_$who.json");
  if($contents_json === false){
    my_log(__FILE__,
           __ROOT__ . "/data/{$date}_$who.json" . " does not exist.\n");
    exit(1);
  }
  $talk = json_decode($contents_json, true);
  if($is_fst) $pdf = "";
  else $pdf = "http://ropas.snu.ac.kr/snt_pdfs/{$date}_$who.pdf";

  $src = array(
    "TITLE" => $talk["title"],
    "SPEAKER" => get_member_name($who),
    "TIME" => date('M j (D)', $t) . " at " . date('H:i', $t),
    "WHERE" => $where,
    "ABSTRACT" => $talk["abstract"],
    "PDF" => $pdf,
  );
  return replace(__ROOT__ . "/template/notice.temp", $src);
}

function gen_snt_msg($is_fst, $snt){
  $msg = array();
  $start_t = time_of_when($snt["when"]);
  $t = $start_t;
  foreach($snt["who"] as $who){
    array_push($msg, gen_talk_msg($is_fst, $t, $snt["where"], $who));
    $t = strtotime('+1 hour', $t);
  }

  $hr = "======================================================================\n\n";
  $msg = implode($hr, $msg);

  if(!$is_fst){
    $msg .= $hr;
    $msg .= "Please leave comments by "
          . date('l', strtotime('-1 day', $start_t))
          . " 18:00.\n";
    $msg .= "http://ropas.snu.ac.kr/snt_comment/comment.html";
  }

  return $msg;
}

function select_snt(){
  $snts = get_schedule();
  foreach($snts as $key => $snt){
    echo ("$key) " . date('Y-m-d H:i', time_of_when($snt["when"])) . "\n");
  }
  echo "\n";
  echo "Select S&T to notice (x to exit): ";
  $input_str = my_fgets();
  echo "\n";

  if($input_str === "x"){
    echo "You entered exit.\n";
    exit(0);
  }else if(my_key_exists($input_str, $snts)){
    $snt = $snts[(int)$input_str];
    echo ( "You selected S&T on "
         . date('Y-m-d H:i', time_of_when($snt["when"])) . ".\n\n" );
    return $snt;
  }else{
    echo "You entered invalid option.\n";
    exit(1);
  }
}

function select_is_fst(){
  echo "Is this the first notice mail? (y or n): ";
  $input_str = my_fgets();
  echo "\n";

  if($input_str === "y"){
    echo "You entered yes.\n\n";
    return true;
  }else if($input_str === "n"){
    echo "You entered no.\n\n";
    return false;
  }else{
    echo "You entered invalid option.\n";
    exit(1);
  }
}

function confirm_notice($is_fst, $snt){
  $msg = gen_snt_msg($is_fst, $snt);
  echo "[MESSAGE START]\n";
  echo $msg . "\n";
  echo "[MESSAGE END]\n\n";
  echo "Are you sure to send the above message? (y or n): ";
  $input_str = my_fgets();
  echo "\n";

  if($input_str === "y"){
    echo "You entered yes.  Notice mail will be sent.\n\n";
    $mail = get_email_conf();
    send_mail('plain', $mail["from"], $mail["to"], 'Show & Tell Notice', $msg);
  }else if($input_str === "n"){
    echo "You entered no.\n\n";
  }else{
    echo "You entered invalid option.\n";
    exit(1);
  }
}

function manual(){
  echo "Welcome to S&T notice system.\n\n";
  $snt = select_snt();
  $is_fst = select_is_fst();
  confirm_notice($is_fst, $snt);
  echo "Bye.\n";
}

function auto($is_fst){
  $mail = get_email_conf();

  if($is_fst) $snts = snts_n_days_later(6);
  else $snts = snts_n_days_later(2);

  foreach($snts as $snt){
    $msg = gen_snt_msg($is_fst, $snt);
    send_mail('plain', $mail["from"], $mail["to"], 'Show & Tell Notice', $msg);
  }
}

if($argc === 1) manual();
else if($argv[1] === "fst") auto(true);
else if($argv[1] === "snd") auto(false);
else{
  my_log(__FILE__, "Command arguments are invalid\n");
  exit(1);
}

?>
