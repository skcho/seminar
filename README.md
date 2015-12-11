Seminar notice system
=====================

## 기본 정보 설정

* `conf/member.json`의 `members`에 세미나 발표자 정보를 추가한다.  오랜
기간 자리를 비우는 경우 `absent`으로 발표자를 옮긴다.

* `conf/queue`에 세미나 발표 순서를 추가한다.

* `conf/info.json`
    * `group`: 그룹 이름
    * `web`: 세미나 시스템의 웹페이지 주소
    * `default`: 기본 세미나 정보(요일/시간/장소/발표자수)
    * `exception`: 예외(`conf/info_sample.json` 참고)
    * `email`: 공지 메일(보내는이/받는이)

## 파일 권한 설정

* seminarbot, www-data(웹서버)에게 필요한 권한
    * `data`, `data/*`의 읽기/쓰기
    * `log`, `log/*`의 읽기/쓰기

* seminarbot에게 필요한 권한
    * `conf/queue`의 읽기/쓰기

## Crontab 설정

* 11일 전 9시, 세미나 리마인드 메일 발송 `remind.php`
* 6일 전 0시, 알림 메일 발송 `notice.php`
* 1일 전 9시, 알림 메일 발송 `notice.php`
* 0일 0시, 세미나 큐 업데이트 `update_queue.php`

seminarbot이 위의 시간에 명령을 수행하도록 crontab을 다음과 같이 설정한다.

    0	0	*	*	*	php /home/skcho/seminar/crontab/update_queue.php && php /home/skcho/seminar/crontab/notice.php 6
    0	9	*	*	*	php /home/skcho/seminar/crontab/notice.php 1
    1	9	*	*	*	php /home/skcho/seminar/crontab/remind.php auto
