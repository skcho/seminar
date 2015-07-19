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


define('SUBJ', '우리들의 코멘트(comments on S&T memos)');

function get_coments($when, $id){
  $talk_data = get_talk_data(time_of_when($when), $id);
  return $talk_data["comments"];
}

function html_of_comment($comment){
  return "<pre style=\"line-height: 160%; font-family: Menlo, Consolas, 'Courier New', monospace; display: block; background-color: #f7f7f9; padding: 9.5px; margin: 0 0 10px; word-break: break-all; word-wrap: break-word; white-space: pre; white-space: pre-wrap; -webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px; border: 1px solid #e1e1e8;\">" . htmlspecialchars($comment) . "</pre>";
}

function html_of_comments($comments){
  return implode("\n", array_map("html_of_comment", $comments));
}

function html_of_speaker($speaker){
  $name = get_member_name($speaker);
  return "<h1 style=\"font-size: 1.5em;\">Comments for $name</h1>";
}

function gen_msg($snt){
  $msg_arr = array();
  foreach($snt["who"] as $id){
    $msg_arr[$id] = get_coments($snt["when"], $id);
  }

  $msg = "";
  foreach($msg_arr as $speaker => $comments){
    $msg .= html_of_speaker($speaker) . "\n";
    $msg .= html_of_comments($comments) . "\n";
  }
  return $msg;
}

function send($msg){
  $mail = get_email_conf();
  send_mail('html', $mail["from"], $mail["to"], SUBJ, $msg);
}


function auto(){
  $snts = snts_n_days_later(1);
  foreach($snts as $snt){
    $msg = gen_msg($snt);
    send($msg);
  }
}

function confirm_notice($snt){
  $msg = gen_msg($snt);
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
