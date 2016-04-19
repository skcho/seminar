<?php

if(!defined('__ROOT__'))
  define('__ROOT__', realpath(dirname(dirname(__FILE__))));

require_once __ROOT__ . "/lib/vocab.php";
require_once __ROOT__ . "/lib/read_data.php";
require_once __ROOT__ . "/lib/log.php";
require_once __ROOT__ . "/lib/validate_input.php";


$title = "등록 페이지";
require __ROOT__ . '/template/header.temp';

function reg_abstract(&$ret_url){
  echo "<p>발표 정보를 등록합니다.</p>\n";

  $id = get_valid_id($_REQUEST["id"]);
  $when = get_valid_time($_REQUEST["when"]);
  $t = strtotime($when);
  $ret_url = "reg_abstract?id=" . $id . "&date=" . date('Y-m-d', $t);

  $talk_data = get_talk_data_or_gen($t, $id);
  $talk_data["title"] = $_REQUEST["title"];
  $talk_data["abstract"] = $_REQUEST["abstract"];

  $result1 = put_talk_data($t, $id, $talk_data);

  $result2 = true;
  if($_FILES["memo"]["type"] === "application/pdf"){
    echo "<p>메모를 등록합니다.</p>\n";
    $memo_filename = gen_memo_filename($t, $id);
    $result2 = move_uploaded_file($_FILES["memo"]["tmp_name"], $memo_filename);
    if($result2){
      chmod($memo_filename, 0664);
      my_log(__FILE__, $memo_filename . " updated\n");
    }else{
      my_log(__FILE__, $memo_filename . " update failed\n");
    }
  }

  return ($result1 && $result2);
}

function reg_comment(&$ret_url){
  echo "<p>코멘트를 등록합니다.</p>\n";

  $id = get_valid_id($_REQUEST["id"]);
  $when = get_valid_time($_REQUEST["when"]);
  $t = strtotime($when);
  $ret_url = "reg_comment?id=" . $id . "&date=" . date('Y-m-d', $t);

  $comment = $_REQUEST["comment"];
  if($comment === ""){
    echo "<p>빈 코멘트입니다.</p>\n";
    return false;
  }else{
    $talk_data = get_talk_data_or_gen($t, $id);
    $comments = $talk_data["comments"];
    array_push($comments, $comment);
    $talk_data["comments"] = $comments;

    return put_talk_data($t, $id, $talk_data);
  }
}

function echo_reg_result($reg_result, $ret_url){
  if($reg_result){
    echo "<p class=\"good\">짝짝짝! 등록 성공.</p>\n";
    echo "<p>3초 후 <a href=\"" . htmlspecialchars($ret_url)
       . "\">이전 페이지</a>로 돌아갑니다...</p>\n";
    echo "<script>setTimeout(function(){window.location.replace(\"" . $ret_url
       . "\");}, 3000);</script>\n";
  }else{
    echo "<p class=\"alert\">등록 실패!</p>\n";
    echo "<p><a href=\"javascript:history.go(-1)\">뒤로 가기</a></p>\n";
  }
}

echo "<div class=\"section\">\n";
$ret_url = "";
if(my_key_exists("comment", $_REQUEST)) $reg_result = reg_comment($ret_url);
else $reg_result = reg_abstract($ret_url);
echo_reg_result($reg_result, $ret_url);
echo "</div>\n";

require __ROOT__ . '/template/footer.temp';

?>
