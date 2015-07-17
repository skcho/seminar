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


function gen_talk_msg($is_fst, $when, $where, $who){
  $date = date('ymd', $when);
  $talk = json_decode(file_get_contents(__ROOT__ . "/data/{$date}_$who.json"),
                      true);
  if($is_fst) $pdf = "";
  else $pdf = "http://ropas.snu.ac.kr/snt_pdfs/{$date}_$who.pdf";

  $src = array(
    "TITLE" => $talk["title"],
    "SPEAKER" => get_member_name($who),
    "TIME" => date('M j (D)', $when) . " at " . date('H:i', $when),
    "WHERE" => $where,
    "ABSTRACT" => $talk["abstract"],
    "PDF" => $pdf,
  );
  return replace(__ROOT__ . "/template/notice.temp", $src);
}

function gen_snt_msg($is_fst, $snt){
  $msg = array();
  $start_when = strtotime( $snt["when"]["year"] . "-"
                         . $snt["when"]["month"] . "-"
                         . $snt["when"]["day"] . "T"
                         . $snt["when"]["hour"] . ":"
                         . $snt["when"]["min"] . ":" . "0" );
  $when = $start_when;
  foreach($snt["who"] as $who){
    array_push($msg, gen_talk_msg($is_fst, $when, $snt["where"], $who));
    $when = strtotime('+1 hour', $when);
  }
  return $msg;
}

function manual(){
  echo "TODO:manual\n";
}

function auto($is_fst){
  $hr = "======================================================================\n\n";
  if($is_fst) $snts = snts_n_days_later(6);
  else $snts = snts_n_days_later(2);
  foreach($snts as $snt){
    $msg = implode($hr, gen_snt_msg($is_fst, $snt));
    $mail = get_email_conf();
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
