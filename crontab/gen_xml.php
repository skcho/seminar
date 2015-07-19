<?php

if(!defined('__ROOT__'))
  define('__ROOT__', realpath(dirname(dirname(__FILE__))));

require_once __ROOT__ . "/lib/file.php";
require_once __ROOT__ . "/lib/interactive.php";
require_once __ROOT__ . "/lib/log.php";
require_once __ROOT__ . "/lib/read_data.php";
require_once __ROOT__ . "/lib/replace.php";
require_once __ROOT__ . "/lib/schedule.php";
require_once __ROOT__ . "/lib/send_mail.php";


function markdown($msg){
  return $msg;
}

function run_talk($snt, $n, $t, $id){
  $talk_data = get_talk_data($t, $id);
  if(get_member_lab($id) === "ropas") $who = "<r:who member=\"" . $id . "\"/>";
  else $who = "<r:who>" . get_member_name($id) . "</r:who>";
  $src = array(
    "TITLE" => htmlspecialchars($talk_data["title"]),
    "WHO" => $who,
    "WHEN" => date(DATE_ATOM, $t),
    "WHERE" => $snt["where"],
    "ABSTRACT" => htmlspecialchars($talk_data["abstract"]),
    "SLIDES_FILENAME" => date('md', $t) . "_" . $n . ".pdf",
    "MEMO_FILENAME" => date('ymd', $t) . "_" . $id . ".pdf",
    "ID" => $id,
    "DATE" => date('Y-m-d', $t),
  );
  $temp_filename = __ROOT__ . "/template/xml.temp";
  $msg = replace($temp_filename, $src);

  $filename = __ROOT__ . "/talk_root/" . date('Y', $t) . "/" . date('md', $t)
            . "_" . $n . ".xml";
  my_file_put_contents($filename, $msg);
}

function run_snt($snt){
  $t = time_of_when($snt["when"]);
  $n = 1;
  foreach($snt["who"] as $id){
    run_talk($snt, $n, $t, $id);
    $n = $n + 1;
    $t = strtotime('+1 hour', $t);
  }
}

function run(){
  $snts = snts_n_days_later(1);
  foreach($snts as $snt) run_snt($snt);
}

run();

?>
