<?php

if(!defined('__ROOT__'))
  define('__ROOT__', realpath(dirname(dirname(__FILE__))));

require_once __ROOT__ . "/lib/file.php";
require_once __ROOT__ . "/lib/log.php";
require_once __ROOT__ . "/lib/queue.php";


function get_conf(){
  $conf = json_get_contents(__ROOT__ . "/conf/info.json");
  return $conf;
}

function get_default_conf(){
  $conf = get_conf();
  return $conf["default"];
}

function get_exception_conf(){
  $conf = get_conf();
  return $conf["exception"];
}

function get_email_conf(){
  $conf = get_conf();
  return $conf["email"];
}

function get_all_members(){
  $members_json = json_get_contents(__ROOT__ . "/conf/member.json");
  return $members_json["members"];
}

function get_member($id){
  $members = get_all_members();
  $members = array_filter($members,
                          function($member) use($id){
                            if($id === $member["id"]) return true;
                            else return false;
                          });
  if(sizeof($members) === 1) return array_pop($members);
  else{
    my_log(__FILE__, "Member ID is invalid\n");
    exit(1);
  }
}

function get_member_name($id){
  $member = get_member($id);
  return $member["name"];
}

function get_member_email($id){
  $member = get_member($id);
  return $member["email"];
}

function get_member_lab($id){
  $member = get_member($id);
  return $member["lab"];
}

function gen_data_filename($t, $id, $ext){
  $date = date('ymd', $t);
  return __ROOT__ . "/data/{$date}_$id." . $ext;
}

function gen_talk_data_filename($t, $id){
  return gen_data_filename($t, $id, "json");
}

function gen_memo_filename($t, $id){
  return gen_data_filename($t, $id, "pdf");
}

function get_talk_data($t, $id){
  $filename = gen_talk_data_filename($t, $id);
  return json_get_contents($filename);
}

function get_talk_data_or_gen($t, $id){
  $filename = gen_talk_data_filename($t, $id);
  if(file_exists($filename)){
    return get_talk_data($t, $id);
  }else{
    $contents = array(
      "title" => "",
      "abstract" => "",
      "commenters" => array(),
      "comments" => array(),
    );
    return $contents;
  }
}

function put_talk_data($t, $id, $talk_data){
  $filename = gen_talk_data_filename($t, $id);
  return json_put_contents($filename, $talk_data);
}

function get_speaker_data($filename){
  if(file_exists($filename)) return json_get_contents($filename);
  else return array();
}

function put_speaker_data($filename, $chair, $speakers){
  $data = get_speaker_data($filename);
  array_push($data, array("chair" => $chair, "speakers" => $speakers));
  return json_put_contents($filename, $data);
}

function get_commenters_today(){
  $cmtrs_all = array();
  $t = time();
  $filename = __ROOT__ . "/data/" . date('ymd', $t) . "_speaker.json";
  if(file_exists($filename)){
    $talks = json_get_contents($filename);
    foreach($talks as $talk){
      $cmtrs_of_talk = array();
      foreach($talk["speakers"] as $id){
        $talk_data = get_talk_data($t, $id);
        $cmtrs_of_talk[get_member_name($id)] =
          array_map("get_member_name", $talk_data["commenters"]);
      }
      $chair_cmtrs = array("chair" => get_member_name($talk["chair"]),
                           "commenters" => $cmtrs_of_talk);
      array_push($cmtrs_all, $chair_cmtrs);
    }
  }
  return $cmtrs_all;
}
