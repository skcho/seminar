<?php
if(!defined('__ROOT__'))
  define('__ROOT__', realpath(dirname(dirname(__FILE__))));

require_once __ROOT__ . "/lib/schedule.php";
require_once __ROOT__ . "/lib/read_data.php";

$title = "일정 보기";
$group = get_group();
require __ROOT__ . '/template/header.temp';
?>

<div class="section">
<h2>변경 사항</h2>

<?php
function str_of_when($when){ return date('Y-m-d H:i', time_of_when($when)); }

echo "<ul>\n";
foreach(get_exception_conf() as $exc){
  echo "<li>";
  if($exc["mode"] == "remove"){
    echo "취소: ". str_of_when($exc["when"]);
  }else if($exc["mode"] == "add"){
    echo "추가: " . str_of_when($exc["when"]) . " @ " . $exc["where"];
  }else if($exc["mode"] == "modify"){
    echo "변경: " . str_of_when($exc["from"]) . htmlspecialchars(" => ")
       . str_of_when($exc["when"]) . " @ " . $exc["where"];
  }else{
    my_log(__FILE__, "Invalid mode name in exception\n");
    exit(1);
  }
  echo "</li>\n";
}
echo "</ul>\n";
?>

</div>

<div class="section">
<h2>일정</h2>

<?php
$snts = get_schedule();
echo "<ul>\n";
foreach($snts as $key => $snt){
  echo "<li>" . date('Y-m-d H:i', time_of_when($snt["when"]))
     . " @ " . $snt["where"] . "\n";
  echo "<ul>\n";
  foreach($snt["who"] as $id){
    echo "<li>" . get_member_name($id) . "</li>\n";
  }
  echo "</ul>\n";
  echo "</li>\n";
}
echo "</ul>\n";
?>

</div>

<?php require __ROOT__ . '/template/footer.temp'; ?>
