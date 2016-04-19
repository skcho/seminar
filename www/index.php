<?php
if(!defined('__ROOT__'))
  define('__ROOT__', realpath(dirname(dirname(__FILE__))));

$title = "쇼앤텔 관리 시스템";
require (__ROOT__ . '/template/header_noback.temp');
?>

<div class="section">
<h2>일정 보기 및 등록</h2>

<ul>
  <li><a href="schedule">일정 보기</a></li>
  <li><a href="reg_abstract">발표 정보 등록 (제목/요약/메모)</a></li>
  <li><a href="reg_comment">코멘트 등록</a></li>
</ul>
</div>
<div class="section">
<h2>알림 메일 발송 일정</h2>

<p>쇼앤텔 발표 날짜 기준으로,</p>

<ul>
  <li>D-11 09:00, 발표자 리마인드</li>
  <li>D-7 18:00, 코멘터 선정</li>
  <li>D-6 00:00, 발표 정보 (제목/요약)</li>
  <li>D-2 09:00, 발표 정보 (제목/요약/메모)</li>
  <li>D-1 18:00, 코멘트 내용</li>
</ul>

</div>

<?php require __ROOT__ . '/template/footer.temp';
