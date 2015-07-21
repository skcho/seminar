<?php $title = "코멘트 보기"; require 'header_noback.temp'; ?>

<?php
if(!defined('__ROOT__'))
  define('__ROOT__', realpath(dirname(dirname(__FILE__))));

require_once __ROOT__ . "/lib/interactive.php";
require_once __ROOT__ . "/lib/log.php";
require_once __ROOT__ . "/lib/read_data.php";
require_once __ROOT__ . "/lib/replace.php";
require_once __ROOT__ . "/lib/schedule.php";
require_once __ROOT__ . "/lib/send_mail.php";
require_once __ROOT__ . "/lib/comment.php";

$id = $_REQUEST["id"];
$t = strtotime($_REQUEST["date"]);

if($id !== NULL) echo gen_msg($t, $id);
?>

<?php require 'footer.temp'; ?>
