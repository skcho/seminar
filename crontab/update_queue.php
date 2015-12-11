<?php

if(!defined('__ROOT__'))
  define('__ROOT__', realpath(dirname(dirname(__FILE__))));

require_once __ROOT__ . "/lib/interactive.php";
require_once __ROOT__ . "/lib/log.php";
require_once __ROOT__ . "/lib/read_data.php";
require_once __ROOT__ . "/lib/replace.php";
require_once __ROOT__ . "/lib/schedule.php";
require_once __ROOT__ . "/lib/send_mail.php";

function run(){
  $queue_filename = __ROOT__ . "/conf/queue";

  $snts = snts_today();
  if(count($snts) >= 1){
    $queue = read_queue($queue_filename);
    foreach($snts as $snt) $snt = set_speaker($snt, $queue);
    write_queue($queue_filename, $queue);
  }
}

run();

?>
