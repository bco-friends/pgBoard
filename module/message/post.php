<?php
function previewpost_post()
{
  global $DB,$Core,$Security,$cmd;

  if(session('id') && (post('name') == "" && post('pass') == "")) $_POST['member_id'] = session('id');
  else
  if($member_id = $Security->form_login(post('name'),post('pass'))) $_POST['member_id'] = $member_id;
  else
  exit_clean();

  // fake post count number a bit of a hack
  if(id()) $cmd[3] = $DB->value("SELECT posts FROM thread WHERE id=$1",array(id()));

  // fake database resultset
  $data                           = array();
  $data[0][BoardQuery::VIEW_ID]   = 99999999; // use new parser
  $data[0][BoardQuery::VIEW_DATE_POSTED]      = time();
  $data[0][BoardQuery::VIEW_CREATOR_ID]       = post('member_id');
  $data[0][BoardQuery::VIEW_CREATOR_NAME]     = $Core->namefromid(post('member_id'));
  $data[0][BoardQuery::VIEW_BODY]             = post('body');
  $data[0][BoardQuery::VIEW_CREATOR_IP]       = "";
  $data[0][BoardQuery::VIEW_SUBJECT]          = "";
  $data[0][BoardQuery::VIEW_THREAD_ID]        = "";
  $data[0][BoardQuery::VIEW_CREATOR_IS_ADMIN] = session('admin') ? 't' : 'f';

  // use standard board display to build preview
  $View = BoardView::init();
  $View->type(Base::VIEW_MESSAGE_PREVIEW);
  $View->data($data);
  $View->thread();
  exit_clean();
}

function create_post()
{
  global $DB, $Security;

  $Data = new Data($DB, $Security);
  if(trim(post('subject')) == "") print "You must enter a subject.";
  else
  if(!$Data->message_insert($_POST)) print "Your message was not submitted.";

  exit_clean();
}

function reply_post()
{
  global $DB, $Security;
  $Data = new Data($DB, $Security);
  if(trim(post('body')) == "") print "You must enter a post body.";
  else
  if(!$Data->message_post_insert($_POST)) print "Your post was not submitted.";
  exit_clean();
}

function addmember_post()
{
  global $Core;
  if(!post('names')) exit();
  $respond = "";
  $members = post('names');
  $members = array_unique(explode(",",$members));
  foreach($members as $member)
  {
    if($id = $Core->idfromname(strtolower(str_replace(SPACE,"",$member))))
    {
      if($id == session('id')) continue;
      $respond .= $id.",".$Core->member_link($member).",";
    }
  }
  print substr($respond,0,-1);
  exit_clean();
}
