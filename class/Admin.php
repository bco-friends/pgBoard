<?php
class BoardAdmin
{
  public function __construct(
    public DB $DB
  ) {}

  function toggle_flag($table,$flag,$id)
  {
    $this->DB->query("UPDATE
                  {$table}
                SET
                  {$flag}=(CASE WHEN {$flag} IS true THEN false ELSE true END)
                WHERE
                  id=$1",array($id));
  }

  function check_flag($table,$flag,$id)
  {
    return $this->DB->value("SELECT {$flag} FROM {$table} WHERE id=$1",array($id)) == "t" ? true : false;
  }
}
