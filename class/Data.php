<?php

class Data
{
  public $insert = array();

  public function __construct(
    public DB $DB,
    public BoardSecurity $Security
  ) {}

  function set_value($key,$val) { $this->insert[$key] = $val; }
  function clear_data() { $this->insert = array(); }

  function thread_insert($data)
  {
    $this->clear_data();
    $this->set_value("subject",$data['subject']);

    if(!isset($data['name'])) $data['name'] = "";
    if(!isset($data['pass'])) $data['pass'] = "";

    if($member_id = $this->Security->form_login($data['name'],$data['pass']))
    {
      $this->set_value("member_id",$member_id);
      $this->set_value("last_member_id",$member_id);

      $this->DB->begin();
      if($this->DB->insert("thread",$this->insert,array_keys($this->insert)))
      {
        $post = array();
        $post['thread_id'] = $this->DB->value("SELECT currval('thread_id_seq')");
        $post['body'] = $data['body'];

        $Search = new Search();
        if($Search->thread_insert($this->insert,$post['thread_id']))
        {
          if($this->thread_post_insert($post,$member_id))
          {
            $this->DB->commit();
            return true;
          }
          else
          $this->DB->rollback();
        }
        else
        $this->DB->rollback();
      }
      else
      $this->DB->rollback();
    }
    else
    {
      $this->DB->rollback();
      return false;
    }
  }

  function thread_post_insert($data,$member_id=false)
  {
    if($this->DB->value("SELECT locked FROM thread WHERE id=$1",array($data['thread_id'])) == 't') return false;

    $this->clear_data();
    $this->set_value("thread_id",$data['thread_id']);
    $this->set_value("body",$data['body']);
    $this->set_value("member_ip",$_SERVER['REMOTE_ADDR']);

    // if no member id defined auth from form
    if(!$member_id)
    if(!$member_id = $this->Security->form_login($data['name'],$data['pass'])) return false;

    $this->set_value("member_id",$member_id);

    $this->DB->begin();
    if($this->DB->insert("thread_post",$this->insert,array_keys($this->insert)))
    {
      $Search = new Search;
      if($Search->thread_post_insert($this->insert,$this->DB->value("SELECT currval('thread_post_id_seq')")))
      {
        $this->DB->commit();
        return true;
      }
      else
      {
        $this->DB->rollback();
        return false;
      }
    }
    else
    {
      $this->DB->rollback();
      return false;
    }
  }

  function thread_post_update($data,$id)
  {
    $this->clear_data();
    $this->set_value("body",$data['body']);

    $this->DB->begin();
    if($this->DB->update("thread_post","id",$id,$data))
    {
      $Search = new Search;
      if($Search->thread_post_update($data,$id))
      {
        $this->DB->commit();
        return true;
      }
      else
      {
        $this->DB->rollback();
        return false;
      }
    }
    else
    {
      $this->DB->rollback();
      return false;
    }
  }

  function message_insert($data)
  {
    $this->clear_data();
    $this->set_value("subject",$data['subject']);

    if(!isset($data['name'])) $data['name'] = "";
    if(!isset($data['pass'])) $data['pass'] = "";

    if($member_id = $this->Security->form_login($data['name'],$data['pass']))
    {
      $this->set_value("member_id",$member_id);
      $this->set_value("last_member_id",$member_id);

      $this->DB->begin();
      if($this->DB->insert("message",$this->insert,array_keys($this->insert)))
      {
        $post = array();
        $post['message_id'] = $this->DB->value("SELECT currval('message_id_seq')");
        $post['body'] = $data['body'];

        $members = explode(",",$data['message_members']);
        $members[] = session('id');
        foreach($members as $member_id)
        {
          $mm = array();
          $mm['message_id'] = $post['message_id'];
          $mm['member_id'] = $member_id;
          $this->DB->insert("message_member",$mm);
        }

        if($this->message_post_insert($post,$member_id))
        {
          $this->DB->commit();
          return true;
        }
        else
        $this->DB->rollback();
      }
    }
    else
    {
      $this->DB->rollback();
      return false;
    }
  }

  function message_post_insert($data,$member_id=false)
  {
    $this->clear_data();
    $this->set_value("message_id",$data['message_id']);
    $this->set_value("body",$data['body']);
    $this->set_value("member_ip",$_SERVER['REMOTE_ADDR']);

    // if no member id defined auth from form
    if(!$member_id)
    if(!$member_id = $this->Security->form_login($data['name'],$data['pass'])) return false;

    $this->set_value("member_id",$member_id);

    if(!$this->DB->check("SELECT true FROM message_member WHERE message_id=$1 AND member_id=$2",array($data['message_id'],$member_id)))
    {
      print "You are not a member of this message.<br/>";
      return false;
    }

    $this->DB->begin();
    if($this->DB->insert("message_post",$this->insert,array_keys($this->insert)))
    {
      $this->DB->commit();
      return true;
    }
    else
    {
      $this->DB->rollback();
      return false;
    }
  }
}
