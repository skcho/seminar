<?php

function get_comments($t, $id){
  $talk_data = get_talk_data($t, $id);
  return $talk_data["comments"];
}

function html_of_comment($comment){
  return "<pre style=\"line-height: 160%; font-family: Menlo, Consolas, 'Courier New', monospace; display: block; background-color: #f7f7f9; padding: 9.5px; margin: 0 0 10px; word-break: break-all; word-wrap: break-word; white-space: pre; white-space: pre-wrap; -webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px; border: 1px solid #e1e1e8;\">" . htmlspecialchars($comment) . "</pre>";
}

function html_of_comments($comments){
  if(count($comments) === 0){
    return "<p>No comments</p>";
  }
  return implode("\n", array_map("html_of_comment", $comments));  
}

function html_of_speaker($speaker){
  $name = get_member_name($speaker);
  return "<h1 style=\"font-size: 1.5em;\">Comments for $name</h1>";
}

function gen_msg($t, $id){
  $comments = get_comments($t, $id);
  $msg = html_of_speaker($id) . "\n";
  $msg .= html_of_comments($comments) . "\n";
  return $msg;
}

?>
