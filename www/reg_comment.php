<?php $title = "코멘트 등록"; require 'header.temp'; ?>

<?php

if(!defined('__ROOT__'))
  define('__ROOT__', realpath(dirname(dirname(__FILE__))));

require_once __ROOT__ . "/lib/schedule.php";
require_once __ROOT__ . "/lib/read_data.php";
require_once __ROOT__ . "/lib/replace.php";

?>

<?php

function entry(){

  echo "<h2>쇼앤텔 일정</h2>\n";
  echo "<p>괄호 안의 사람들이 코멘터입니다.</p>";

  $snts = get_schedule();
  echo "<ul>\n";
  foreach($snts as $key => $snt){
    $t = time_of_when($snt["when"]);
    echo "<li>" . date('Y-m-d H:i', $t)
       . " @ " . $snt["where"] . "\n";
    echo "<ul>\n";
    $date = date('Y-m-d', $t);
    foreach($snt["who"] as $id){
      echo "<li><a href=\"reg_comment?id={$id}&amp;date={$date}\">"
         . get_member_name($id)
         . "</a> ";
      $filename = gen_talk_data_filename($t, $id);
      if(file_exists($filename)){
        $talk_data = get_talk_data($t, $id);
        $commenters = $talk_data["commenters"];
        if(count($commenters) !== 0){
          $commenters = array_map(
            function($commenter){ return get_member_name($commenter); },
            $commenters
          );
          echo "(" . implode(", ", $commenters) . ")";
        }
      }

      echo "</li>\n";
    }
    echo "</ul>\n";
    echo "</li>\n";
  }
  echo "</ul>\n";

}

function reg(){

  echo "<h2>코멘트 등록 양식</h2>\n";

  $id = $_REQUEST["id"];
  $date = $_REQUEST["date"];
  $t = strtotime($date);
  $talk_data = get_talk_data_or_gen($t, $id);
  if(file_exists(gen_memo_filename($t, $id))){
    $link = "http://ropas.snu.ac.kr/snt_pdfs2/" . date('ymd', $t) . "_$id.pdf";
    $memo = "<a href=\"" . $link . "\">" . $link . "</a>";
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
  echo replace(__ROOT__ . "/template/reg_comment.temp", $arr);
}

if(my_key_exists("id", $_REQUEST)){
  reg();
}
entry();

?>

<?php require 'footer.temp'; ?>
