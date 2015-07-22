<?php

/* Send comment mail

   1. If it runs automatically,
   > php comment.php auto

   2. If it runs manually,
   > php comment.php
 */

if(!defined('__ROOT__'))
  define('__ROOT__', realpath(dirname(dirname(__FILE__))));

require_once __ROOT__ . "/lib/interactive.php";
require_once __ROOT__ . "/lib/log.php";
require_once __ROOT__ . "/lib/read_data.php";
require_once __ROOT__ . "/lib/replace.php";
require_once __ROOT__ . "/lib/schedule.php";
require_once __ROOT__ . "/lib/send_mail.php";
require_once __ROOT__ . "/lib/comment.php";

function gen_total_msg($snt){
  $t = time_of_when($snt["when"]);
  $msg = "";
  foreach($snt["who"] as $id){
    $msg .= gen_comment_msg($t, $id);
  }
  return $msg;
}

function send($msg){
  $mail = get_email_conf();
  send_mail( 'html', $mail["from"], $mail["to"]
           , '우리들의 코멘트(comments on S&T memos)', $msg );
}

function auto(){
  $snts = snts_n_days_later(1);
  foreach($snts as $snt){
    $msg = gen_total_msg($snt);
    send($msg);
  }
}

function confirm_notice($snt){
  $msg = gen_total_msg($snt);
  echo_msg($msg);
  $confirm = ask_y_or_n("Are you sure to send the above message?");
  if($confirm){
    echo "Comment mail will be sent.\n\n";
    send($msg);
  }else{
    echo "Comment mail is cancelled.\n\n";
  }
}

function manual(){
  echo "Welcome to S&T comment system.\n\n";
  $snt = select_snt();
  confirm_notice($snt);
  echo "Bye.\n";
}

if($argc === 1) manual();
else if($argv[1] === "auto") auto();
else{
  my_log(__FILE__, "Command arguments are invalid\n");
  exit(1);
}


?>
