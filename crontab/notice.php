<?php

/* Send notice mail

   1. If it runs automatically to send a seminar notice that will be
   held at 7 days later,
   > php notice.php 7

   2. If it runs manually, > php notice.php */

if(!defined('__ROOT__'))
  define('__ROOT__', realpath(dirname(dirname(__FILE__))));

require_once __ROOT__ . "/lib/interactive.php";
require_once __ROOT__ . "/lib/log.php";
require_once __ROOT__ . "/lib/read_data.php";
require_once __ROOT__ . "/lib/replace.php";
require_once __ROOT__ . "/lib/schedule.php";
require_once __ROOT__ . "/lib/send_mail.php";


function gen_talk_msg($t, $where, $who){
  $talk = get_talk_data($t, $who);

  $src = array(
    "TITLE" => $talk["title"],
    "SPEAKER" => get_member_name($who),
    "TIME" => date('M j (D)', $t) . " at " . date('H:i', $t),
    "WHERE" => $where,
    "ABSTRACT" => $talk["abstract"],
  );
  return replace(__ROOT__ . "/template/notice.temp", $src);
}

function gen_snt_msg($snt){
  $msg_arr = array();
  $start_t = time_of_when($snt["when"]);
  $t = $start_t;
  foreach($snt["who"] as $who){
    array_push($msg_arr, gen_talk_msg($t, $snt["where"], $who));
    $t = strtotime('+1 hour', $t);
  }

  $hr = "\n======================================================================\n\n";
  $msg = implode($hr, $msg_arr);

  return $msg;
}

function send_notice($snt, $msg){
  $mail = get_email_conf();
  send_mail('plain', $mail["from"], $mail["to"], 'Seminar Notice', $msg);
}

function confirm_notice($snt){
  $msg = gen_snt_msg($snt);
  echo_msg($msg);
  $confirm = ask_y_or_n("Are you sure to send the above message?");
  if($confirm){
    echo "Notice mail will be sent.\n\n";
    send_notice($snt, $msg);
  }else{
    echo "Seminar notice is cancelled.\n\n";
  }
}

function manual(){
  echo "Welcome to seminar notice system.\n\n";
  $snt = select_snt();
  confirm_notice($snt);
  echo "Bye.\n";
}

function auto($days){
  $snts = snts_n_days_later($days);

  foreach($snts as $snt){
    $msg = gen_snt_msg($snt);
    send_notice($snt, $msg);
  }
}

if($argc === 1) manual();
else auto($argv[1]);

?>
