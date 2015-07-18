<?php

/* Remind S&T

   1. If it runs automatically,

   > php remind.php auto

   2. If it runs manually,

   > php remind.php
 */

if(!defined('__ROOT__'))
  define('__ROOT__', realpath(dirname(dirname(__FILE__))));

require_once __ROOT__ . "/lib/schedule.php";
require_once __ROOT__ . "/lib/log.php";
require_once __ROOT__ . "/lib/replace.php";
require_once __ROOT__ . "/lib/member.php";
require_once __ROOT__ . "/lib/send_mail.php";
require_once __ROOT__ . "/lib/conf.php";
require_once __ROOT__ . "/lib/etc.php";


define('SUBJ', 'Show & Tell Order Remind');

function gen_msg($snt){
  $names = array_map( function($id){return get_member_name($id);}
                    , $snt["who"] );
  $talk_time = time_of_when($snt["when"]);
  $abs_time = mktime(23, 59, 0,
                     $snt["when"]["month"],
                     $snt["when"]["day"] - 7,
                     $snt["when"]["year"]);
  $memo_time = mktime(9, 0, 0,
                      $snt["when"]["month"],
                      $snt["when"]["day"] - 2,
                      $snt["when"]["year"]);
  $msg = replace(__ROOT__ . "/template/remind.temp",
                 array(
                   "names" => implode(', ', $names),
                   "talk_time" => date('Y-m-d', $talk_time),
                   "abs_time" => date('Y-m-d H:i', $abs_time),
                   "memo_time" => date('Y-m-d H:i', $memo_time),
                 ));
  return $msg;
}

function send_remind($snt, $msg){
  $mail = get_email_conf();
  $emails = array_map( function($id){return get_member_email($id);}
                     , $snt["who"] );
  /* TODO: uncomment before release */
  // send_mail('plain', $mail["from"], $emails, SUBJ, $msg);
}

function auto(){
  $snts = snts_n_days_later(11);
  foreach($snts as $snt){
    $msg = gen_msg($snt);
    send_remind($snt, $msg);
  }
}

function confirm_remind($snt){
  $msg = gen_msg($snt);
  echo "[MESSAGE START]\n";
  echo $msg;
  echo "[MESSAGE END]\n\n";
  $confirm = ask_y_or_n("Are you sure to send the above message?");
  if($confirm){
    echo "Remind mail will be sent.\n\n";
    send_remind($snt, $msg);
  }else{
    echo "S&T remind is cancelled.\n";
  }
}

function manual(){
  echo "Welcome to S&T remind system.\n\n";
  $snt = select_snt();
  confirm_remind($snt);
  echo "Bye.\n";
}

if($argc === 1) manual();
else if($argv[1] === "auto") auto();
else{
  my_log(__FILE__, "Command arguments are invalid\n");
  exit(1);
}

?>