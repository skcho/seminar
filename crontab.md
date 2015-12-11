If the absolute directory of the seminar system is "/home/skcho/seminar",

    0	0	*	*	*	php /home/skcho/seminar/crontab/update_queue.php && php /home/skcho/seminar/crontab/notice.php 6
    0	9	*	*	*	php /home/skcho/seminar/crontab/notice.php 1
    1	9	*	*	*	php /home/skcho/seminar/crontab/remind.php auto
