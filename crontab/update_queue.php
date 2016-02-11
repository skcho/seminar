<?php

if(!defined('__ROOT__'))
  define('__ROOT__', realpath(dirname(dirname(__FILE__))));

require_once __ROOT__ . "/lib/log.php";
require_once __ROOT__ . "/lib/read_data.php";
require_once __ROOT__ . "/lib/replace.php";
require_once __ROOT__ . "/lib/schedule.php";
require_once __ROOT__ . "/lib/send_mail.php";

function run(){
  $all_filename = __ROOT__ . "/conf/queue.all";
  $ropas_filename = __ROOT__ . "/conf/queue.ropas";
  $sf_filename = __ROOT__ . "/conf/queue.sf";
  $chair_filename = __ROOT__ . "/conf/queue.chair";
  $speaker_filename = __ROOT__ . "/data/" . date('ymd') . "_speaker.json";

  $snts = snts_today();
  if(count($snts) >= 1){
    $queue_all = read_queue($all_filename);
    $queue_ropas = read_queue($ropas_filename);
    $queue_sf = read_queue($sf_filename);
    $queue_chair = read_queue($chair_filename);

    foreach($snts as $snt){
      $snt =
        set_speaker($snt, $queue_all, $queue_ropas, $queue_sf, $queue_chair);
      put_speaker_data($speaker_filename, $snt["chair"], $snt["who"]);
    }

    write_queue($all_filename, $queue_all);
    write_queue($ropas_filename, $queue_ropas);
    write_queue($sf_filename, $queue_sf);
    write_queue($chair_filename, $queue_chair);
  }
}

run();

?>
