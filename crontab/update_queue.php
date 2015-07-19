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
  $all_filename = __ROOT__ . "/conf/queue.all";
  $ropas_filename = __ROOT__ . "/conf/queue.ropas";
  $sf_filename = __ROOT__ . "/conf/queue.sf";

  $queue_all = read_queue($all_filename);
  $queue_ropas = read_queue($ropas_filename);
  $queue_sf = read_queue($sf_filename);

  foreach(snts_today() as $snt)
    set_speaker($snt, $queue_all, $queue_ropas, $queue_sf);

  write_queue($all_filename, $queue_all);
  write_queue($ropas_filename, $queue_ropas);
  write_queue($sf_filename, $queue_sf);
}

run();

?>
