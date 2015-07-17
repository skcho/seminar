<?php

if(!defined('__ROOT__'))
  define('__ROOT__', realpath(dirname(dirname(__FILE__))));

require_once __ROOT__ . "/lib/log.php";


/* Send mail

 - $mode: 'plain' or 'html'
 - $from: string of mail address
 - $to: array of strings of mail addresses
 - $subj: UTF-8 formatted subject
 - $msg: UTF-8 formatted mail body
 */
function send_mail($mode, $from, $to, $subj, $msg){
  $headers  = 'MIME-Version: 1.0' . "\r\n";
  $headers .= 'Content-type: text/' . $mode . '; charset=UTF-8' . "\r\n";
  $headers .= "From: " . $from . "\r\n";
  $subj = "=?UTF-8?B?".base64_encode($subj)."?=";
  if(mail(implode(",", $to), $subj, $msg, $headers)){
    my_log(__FILE__, "Succeed send notice mail");
  }else{
    my_log(__FILE__, "Fail send notice mail");
    exit(1);
  }
}

?>
