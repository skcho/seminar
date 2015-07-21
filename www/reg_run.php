<?php $title = "발표 정보 등록"; require 'header.temp'; ?>

<p>발표 정보를 등록합니다.</p>

<?php

if(!defined('__ROOT__'))
  define('__ROOT__', realpath(dirname(dirname(__FILE__))));

require_once __ROOT__ . "/lib/read_data.php";

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

if($result1 && $result2){
  echo '<script>alert("등록 성공.")</script>';
}else{
  echo '<script>alert("등록 실패!")</script>';
}

header("refresh:0; url=reg_abstract?id=$id&date=$when");
exit(1);
?>

<?php require 'footer.temp'; ?>
