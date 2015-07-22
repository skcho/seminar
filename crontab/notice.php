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

require_once __ROOT__ . "/lib/interactive.php";
require_once __ROOT__ . "/lib/log.php";
require_once __ROOT__ . "/lib/read_data.php";
require_once __ROOT__ . "/lib/replace.php";
require_once __ROOT__ . "/lib/schedule.php";
require_once __ROOT__ . "/lib/send_mail.php";


function gen_talk_msg($is_fst, $t, $where, $who){
  $talk = get_talk_data($t, $who);
  if($is_fst) $pdf = "";
  else{
    $date = date('ymd', $t);
    $pdf = "http://ropas.snu.ac.kr/snt_memo2/{$date}_$who.pdf";
  }

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
  $msg_arr = array();
  $start_t = time_of_when($snt["when"]);
  $t = $start_t;
  foreach($snt["who"] as $who){
    array_push($msg_arr, gen_talk_msg($is_fst, $t, $snt["where"], $who));
    $t = strtotime('+1 hour', $t);
  }

  $hr = "======================================================================\n\n";
  $msg = implode($hr, $msg_arr);

  if(!$is_fst){
    $msg .= $hr;
    $msg .= "Please leave comments by "
          . date('Y-m-d', strtotime('-1 day', $start_t))
          . " 18:00.\n";
    $msg .= "http://ropas.snu.ac.kr/snt_system2/reg_comment" . "\n";
  }

  return $msg;
}

function select_is_fst(){
  $is_fst = ask_y_or_n("Is this the first notice mail?");
  if($is_fst) echo "The first notice mail is selected.\n\n";
  else echo "The second notice mail is selected.\n\n";
  return $is_fst;
}

function send_notice($is_fst, $snt, $msg){
  $mail = get_email_conf();
  send_mail('plain', $mail["from"], $mail["to"], 'Show & Tell Notice', $msg);
}

function confirm_notice($is_fst, $snt){
  $msg = gen_snt_msg($is_fst, $snt);
  echo_msg($msg);
  $confirm = ask_y_or_n("Are you sure to send the above message?");
  if($confirm){
    echo "Notice mail will be sent.\n\n";
    send_notice($is_fst, $snt, $msg);
  }else{
    echo "S&T notice is cancelled.\n\n";
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
  if($is_fst) $snts = snts_n_days_later(6);
  else $snts = snts_n_days_later(2);

  foreach($snts as $snt){
    $msg = gen_snt_msg($is_fst, $snt);
    send_notice($is_fst, $snt, $msg);
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
