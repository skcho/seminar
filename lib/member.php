<?php

if(!defined('__ROOT__'))
  define('__ROOT__', realpath(dirname(dirname(__FILE__))));

function get_member($id){
  $members_json = json_decode(file_get_contents(__ROOT__ . "/conf/member.json"),
                              true);
  $members = $members_json["members"];
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

?>
