<?php

if(!defined('__ROOT__'))
  define('__ROOT__', realpath(dirname(dirname(__FILE__))));

function get_conf(){
  $conf = json_decode(file_get_contents(__ROOT__ . "/conf/info.json"), true);
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
  $members_json = json_decode(file_get_contents(__ROOT__ . "/conf/member.json"),
                              true);
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

function gen_talk_data_filename($t, $id){
  $date = date('ymd', $t);
  return __ROOT__ . "/data/{$date}_$id.json";
}

function get_talk_data($t, $id){
  $filename = gen_talk_data_filename($t, $id);
  $contents_json = file_get_contents($filename);
  if($contents_json === false){
    my_log(__FILE__, "$filename is not found\n");
    exit(1);
  }
  return json_decode($contents_json, true);
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
  if(file_put_contents($filename, json_encode($talk_data)) === false){
    my_log(__FILE__, "$filename cannot be written.\n");
    exit(1);
  }
  my_log(__FILE__, "$filename updated.\n");
}

?>
