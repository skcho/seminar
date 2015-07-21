<?php $title = "등록 페이지"; require 'header.temp'; ?>

<?php

if(!defined('__ROOT__'))
  define('__ROOT__', realpath(dirname(dirname(__FILE__))));

require_once __ROOT__ . "/lib/read_data.php";
require_once __ROOT__ . "/lib/interactive.php";


function reg_result_pop_up($result){
  if($result) echo '<script>alert("등록 성공.")</script>';
  else echo '<script>alert("등록 실패!")</script>';
}

function reg_abstract(){
  echo "<p>발표 정보를 등록합니다.</p>\n";

  $id = $_REQUEST["id"];
  $when = $_REQUEST["when"];
  $t = strtotime($when);

  $talk_data = get_talk_data_or_gen($t, $id);
  $talk_data["title"] = $_REQUEST["title"];
  $talk_data["abstract"] = $_REQUEST["abstract"];

  $result1 = put_talk_data($t, $id, $talk_data);

  $result2 = true;
  if($_FILES["memo"]["type"] === "application/pdf"){
    echo "<p>메모를 등록합니다.</p>\n";
    $memo_filename = gen_memo_filename($t, $id);
    $result2 = move_uploaded_file($_FILES["memo"]["tmp_name"], $memo_filename);
    if($result2) chmod($memo_filename, 0664);
  }

  reg_result_pop_up($result1 && $result2);

  header("refresh:0; url=reg_abstract?id=$id&date=$when");
  exit(1);
}

function reg_comment(){
  echo "<p>코멘트를 등록합니다.</p>\n";

  $id = $_REQUEST["id"];
  $when = $_REQUEST["when"];
  $t = strtotime($when);

  $comment = $_REQUEST["comment"];
  if($comment === ""){
    echo '<script>alert("빈 코멘트.  등록 실패!")</script>';
    header("refresh:0; url=reg_comment?id=$id&date=$when");
    exit(1);
  }
  $talk_data = get_talk_data_or_gen($t, $id);
  $comments = $talk_data["comments"];
  array_push($comments, $comment);
  $talk_data["comments"] = $comments;

  $result = put_talk_data($t, $id, $talk_data);

  reg_result_pop_up($result);

  header("refresh:0; url=reg_comment?id=$id&date=$when");
  exit(1);
}

if(my_key_exists("comment", $_REQUEST)) reg_comment();
else reg_abstract();

?>

<?php require 'footer.temp'; ?>
