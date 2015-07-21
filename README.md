# ROPAS seminar notice system

## 쇼앤텔 알림 일정

* 11일 전 9시, 쇼앤텔 리마인드 메일 발송 `remind.php`
* 7일 전 18시, 코멘터 알림 메일 발송 `commenter.php`
* 6일 전 0시, 첫 번째 알림 메일 발송 `notice.php`
* 2일 전 9시, 두 번째 알림 메일 발송 `notice.php`
* 1일 전 18시, ropas 아카이브에 등록 `gen_xml.php`
* 1일 전 18시, 코멘트 메일 발송 `comment.php`
* 0일 0시, 쇼앤텔 큐 업데이트 `update_queue.php`

## 파일 권한

다음 권한이 seminarbot과 www-data에게 모두 필요하다.

* `data`, `data/*`의 읽기/쓰기
* `log`, `log/*`의 읽기/쓰기
