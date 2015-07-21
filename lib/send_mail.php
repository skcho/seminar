<?php

if(!defined('__ROOT__'))
  define('__ROOT__', realpath(dirname(dirname(__FILE__))));

require_once __ROOT__ . "/lib/log.php";


/* Send mail

 - $mode: 'plain' or 'html'
 - $from: arrya of name and mail address
 - $to: array of strings of mail addresses
 - $subj: UTF-8 formatted subject
 - $msg: UTF-8 formatted mail body
 */
function send_mail($mode, $from, $to, $subj, $msg){
  $headers  = 'MIME-Version: 1.0' . "\r\n";
  $headers .= 'Content-type: text/' . $mode . '; charset=UTF-8' . "\r\n";
  $headers .= "From: =?UTF-8?B?" . base64_encode($from["name"]) . "?="
            . " <" . $from["email"] . ">\r\n";
  $subj = "=?UTF-8?B?".base64_encode($subj)."?=";
  if(mail(implode(",", $to), $subj, $msg, $headers)){
    my_log(__FILE__, "Succeed to send mail\n");
  }else{
    my_log(__FILE__, "Fail to send mail\n");
    exit(1);
  }
}

?>
