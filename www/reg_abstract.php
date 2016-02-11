<?php
if(!defined('__ROOT__'))
  define('__ROOT__', realpath(dirname(dirname(__FILE__))));

require_once __ROOT__ . "/lib/vocab.php";
require_once __ROOT__ . "/lib/schedule.php";
require_once __ROOT__ . "/lib/read_data.php";
require_once __ROOT__ . "/lib/replace.php";
require_once __ROOT__ . "/www/validate_input.php";

$title = "발표 정보 등록";
require __ROOT__ . '/template/header.temp';

function entry(){
  echo "<div class=\"section\">\n";
  echo "<h2>쇼앤텔 일정</h2>\n";

  $snts = get_schedule();
  echo "<ul>\n";
  foreach($snts as $key => $snt){
    $t = time_of_when($snt["when"]);
    echo "<li>" . date('Y-m-d H:i', $t)
       . " @ " . $snt["where"] . "\n";
    echo "<ul>\n";
    $date = date('Y-m-d', $t);
    foreach($snt["who"] as $id){
      echo "<li><a href=\"reg_abstract?id={$id}&amp;date={$date}\">"
         . get_member_name($id)
         . "</a></li>\n";
    }
    echo "</ul>\n";
    echo "</li>\n";
  }
  echo "</ul>\n";
  echo "</div>\n";
}

function reg(){
  $id = get_valid_id($_REQUEST["id"]);
  $date = get_valid_date($_REQUEST["date"]);
  $t = strtotime($date);
  $talk_data = get_talk_data_or_gen($t, $id);
  if(file_exists(gen_memo_filename($t, $id))){
    $memo_filename = date('ymd', $t) . "_$id.pdf";
    $link = "http://ropas.snu.ac.kr/snt_memo2/" . $memo_filename;
    $memo = "<a href=\"" . $link . "\">" . $memo_filename . "</a>";
  }else{
    $memo = "등록된 메모가 없습니다.";
  }

  $arr = array("ID" => $id,
               "NAME" => get_member_name($id),
               "WHEN" => $date,
               "TITLE" => htmlspecialchars($talk_data["title"]),
               "ABSTRACT" => htmlspecialchars($talk_data["abstract"]),
               "MEMO" => $memo,
  );
  echo replace(__ROOT__ . "/template/reg_abstract.temp", $arr);
}

if(my_key_exists("id", $_REQUEST)) reg();
entry();

require __ROOT__ . '/template/footer.temp';
