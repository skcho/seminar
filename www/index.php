<?php
if(!defined('__ROOT__'))
  define('__ROOT__', realpath(dirname(dirname(__FILE__))));

require_once __ROOT__ . "/lib/read_data.php";

$title = "세미나 관리 시스템";
$group = get_group();
require (__ROOT__ . '/template/header_noback.temp');
?>

<div class="section">
<h2>일정 보기 및 등록</h2>

<ul>
  <li><a href="schedule">일정 보기</a></li>
  <li><a href="reg_abstract">발표 정보 등록 (제목/요약)</a></li>
</ul>
</div>
<div class="section">
<h2>알림 메일 발송 일정</h2>

<p>세미나 발표 날짜 기준으로,</p>

<ul>
  <li>D-11 09:00, 세미나 등록 리마인드 메일 발송</li>
  <li>D-6 00:00, 알림 메일 발송</li>
  <li>D-1 09:00, 알림 메일 발송</li>
</ul>

</div>

<?php require __ROOT__ . '/template/footer.temp'; ?>
