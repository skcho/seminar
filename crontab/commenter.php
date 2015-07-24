<?php

/* Set commenters

   1. If it runs automatically,
   > php commenter.php auto

   2. If it runs manually,
   > php commenter.php
 */

if(!defined('__ROOT__'))
  define('__ROOT__', realpath(dirname(dirname(__FILE__))));

require_once __ROOT__ . "/lib/interactive.php";
require_once __ROOT__ . "/lib/log.php";
require_once __ROOT__ . "/lib/read_data.php";
require_once __ROOT__ . "/lib/replace.php";
require_once __ROOT__ . "/lib/schedule.php";
require_once __ROOT__ . "/lib/send_mail.php";


function gen_commenter($speakers){
  $all = array_map(function($m){return $m["id"];}, get_all_members());
  $listeners = array_filter($all,
                            function($id) use($speakers){
                              return !in_array($id, $speakers);
                            });

  if(count($speakers) * 3 > count($listeners)){
    my_log(__FILE__, "Listeners are too less compared to speakers\n");
    exit(1);
  }

  shuffle($listeners);
  $commenter_info = array();
  foreach($speakers as $speaker){
    $commenter_info[$speaker] = array_slice($listeners, 0, 3);
    $listeners = array_slice($listeners, 3);
  }
  return $commenter_info;
}

function gen_msg($snt, $commenter_info){
  $talk_time = time_of_when($snt["when"]);
  $comment_time = mktime(18, 0, 0,
                         $snt["when"]["month"],
                         $snt["when"]["day"] - 1,
                         $snt["when"]["year"]);
  $commenters_msg = "";
  foreach($commenter_info as $speaker => $commenters){
    $speaker_name = get_member_name($speaker);
    $commenters_name = array_map("get_member_name", $commenters);
    $commenters_msg .= $speaker_name . ": "
                     . implode(", ", $commenters_name) . "\n";
  }

  $msg = replace(__ROOT__ . "/template/commenter.temp",
                 array(
                   "talk_time" => date("Y-m-d H:i", $talk_time),
                   "comment_time" => date("Y-m-d H:i", $comment_time),
                   "commenter_info" => $commenters_msg,
                 ));
  return $msg;
}

function send_commenter($snt, $commenter_info, $msg){
  $all = array();
  foreach($commenter_info as $speaker => $commenters){
    array_push($all, $speaker);
    foreach($commenters as $commenter){
      array_push($all, $commenter);
    }
  }
  $all_email = array_map("get_member_email", $all);
  $mail = get_email_conf();
  send_mail( 'plain', $mail["from"], $all_email
           , 'Show & Tell Commenter Notice', $msg );
}

function set_data($snt, $commenter_info){
  $t = time_of_when($snt["when"]);
  foreach($commenter_info as $speaker => $commenters){
    $contents = get_talk_data_or_gen($t, $speaker);
    $contents["commenters"] = $commenters;
    if(!put_talk_data($t, $speaker, $contents)) exit(1);
  }
}

function send_and_set_data($snt, $commenter_info, $msg){
  send_commenter($snt, $commenter_info, $msg);
  set_data($snt, $commenter_info);
}

function auto(){
  $snts = snts_n_days_later(7);
  foreach($snts as $snt){
    $commenter_info = gen_commenter($snt["who"]);
    $msg = gen_msg($snt, $commenter_info);
    send_and_set_data($snt, $commenter_info, $msg);
  }
}

function confirm_commenter($snt){
  $commenter_info = gen_commenter($snt["who"]);
  $msg = gen_msg($snt, $commenter_info);
  echo_msg($msg);
  $confirm = ask_y_or_n("Are you sure to send the above message?");
  if($confirm){
    echo "Commenter notice mail will be sent.\n\n";
    send_and_set_data($snt, $commenter_info, $msg);
  }else{
    echo "Commenter notice is cancelled.\n";
  }
}

function manual(){
  echo "Welcome to S&T commenter system.\n\n";
  $snt = select_snt();
  confirm_commenter($snt);
  echo "Bye.\n";
}

if($argc === 1) manual();
else if($argv[1] === "auto") auto();
else{
  my_log(__FILE__, "Command arguments are invalid\n");
  exit(1);
}

?>
