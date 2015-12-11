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

function get_group(){
  $conf = get_conf();
  return $conf["group"];
}

function get_web(){
  $conf = get_conf();
  return $conf["web"];
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

function gen_data_filename($t, $id, $ext){
  $date = date('ymd', $t);
  return __ROOT__ . "/data/{$date}_$id." . $ext;
}

function gen_talk_data_filename($t, $id){
  return gen_data_filename($t, $id, "json");
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

function put_speaker_data($filename, $speakers){
  $data = get_speaker_data($filename);
  array_push($data, array("speakers" => $speakers));
  return json_put_contents($filename, $data);
}

?>
