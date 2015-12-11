Seminar notice system
=====================

## 세미나 알림 일정

* 11일 전 9시, 세미나 리마인드 메일 발송 `remind.php`
* 6일 전 0시, 알림 메일 발송 `notice.php`
* 1일 전 9시, 알림 메일 발송 `notice.php`
* 0일 0시, 세미나 큐 업데이트 `update_queue.php`

[crontab 설정 예](crontab.md)

## 파일 권한 설정

* seminarbot, www-data에게 필요한 권한
    * `data`, `data/*`의 읽기/쓰기
    * `log`, `log/*`의 읽기/쓰기
