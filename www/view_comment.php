<?php
if(!defined('__ROOT__'))
  define('__ROOT__', realpath(dirname(dirname(__FILE__))));

require_once __ROOT__ . "/lib/log.php";
require_once __ROOT__ . "/lib/read_data.php";
require_once __ROOT__ . "/lib/replace.php";
require_once __ROOT__ . "/lib/schedule.php";
require_once __ROOT__ . "/lib/send_mail.php";
require_once __ROOT__ . "/lib/comment.php";
require_once __ROOT__ . "/lib/validate_input.php";

$title = "코멘트 보기";
require __ROOT__ . '/template/header_noback.temp';

$id = get_valid_id($_REQUEST["id"]);
$t = strtotime(get_valid_date($_REQUEST["date"]));

if($id !== NULL) echo gen_comment_msg($t, $id);

require __ROOT__ . '/template/footer.temp';
?>
