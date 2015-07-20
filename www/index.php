<?php $title = "쇼앤텔 관리 시스템"; require 'header.temp'; ?>

<h1>쇼앤텔 관리 시스템</h1>

<h2>일정 보기 및 등록</h2>

<ul>
  <li><a href="schedule">일정 보기</a></li>
  <li><a href="reg_abstract">발표 정보 (제목/요약/메모) 입력</a></li>
  <li><a href="reg_comment">코멘트 입력</a></li>
</ul>

<h2>알림 메일 발송 일정</h2>

<ul>
  <li>11일 전 9시, 쇼앤텔 리마인드 메일 발송</li>
  <li>7일 전 18시, 코멘터 알림 메일 발송</li>
  <li>6일 전 0시, 첫 번째 알림 메일 발송</li>
  <li>2일 전 9시, 두 번째 알림 메일 발송</li>
  <li>1일 전 18시, 코멘트 메일 발송</li>
</ul>

<?php require 'footer.temp'; ?>