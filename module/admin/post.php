<?php
function editpost_post()
{
  global $DB, $Security;
  $Data = new Data($DB, $Security);
  if(trim(post('body')) == "")  print "You must enter a post body.";
  else
  if(!$Data->thread_post_update($_POST,id())) print "Your thread was not submitted.";

  exit_clean();
}
