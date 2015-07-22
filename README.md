# ROPAS seminar notice system

## 쇼앤텔 알림 일정

* 11일 전 9시, 쇼앤텔 리마인드 메일 발송 `remind.php`
* 7일 전 18시, 코멘터 알림 메일 발송 `commenter.php`
* 6일 전 0시, 첫 번째 알림 메일 발송 `notice.php`
* 2일 전 9시, 두 번째 알림 메일 발송 `notice.php`
* 1일 전 18시, ropas 아카이브에 등록 `gen_xml.php`
* 1일 전 18시, 코멘트 메일 발송 `comment.php`
* 0일 0시, 쇼앤텔 큐 업데이트 `update_queue.php`

주의: 0일 0시에 반드시 `update_queue.php`가 먼저 실행되어야 한다.

## 파일 권한 설정

* seminarbot, www-data에게 필요한 권한
    * `data`, `data/*`의 읽기/쓰기
    * `log`, `log/*`의 읽기/쓰기
* seminarbot에게 필요한 권한
    * `talk_root/201X`의 쓰기
* members에게 필요한 권한
    * `data`의 쓰기

## 심볼릭 링크 설정

`talk_root`는 로파스 홈페이지의 talk 최상위 페이지를 가리키도록
설정한다.
