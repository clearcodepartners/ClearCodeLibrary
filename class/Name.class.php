<?php

/**
 * @property string prefix
 * @property string first
 * @property string middle
 * @property string last
 * @property string suffix
 * @property string title
 * @property string nickname
 * @property string salutation
 */
class Name extends Lib{
    public function fresh(){
        $this->prefix           = "";
        $this->first            = "";
        $this->middle           = "";
        $this->last             = "";
        $this->suffix           = "";
        $this->title            = "";
        $this->nickname         = "";
        $this->salutation       = "";
    }
    public function __toString(){           return $this->prefix .' '. $this->first . ' ' . $this->get_nickname() . ' ' . $this->middle . ' ' . $this->last . ' ' . $this->suffix . ' "'  . $this->title. '"'; }
    public function alpha(){                return $this->last . ' ' . $this->get_suffix() . ', ' . $this->get_prefix() . $this->first . ' ' . $this->get_middle_initial() . $this->get_title(); }
    public function initials(){             return strtoupper( $this->get_first_initial().$this->get_middle_initial().$this->get_last_initial()); }
    public function mail(){                 return $this->get_prefix() . $this->first .' '. $this->get_middle_initial() . ' ' . $this->last . $this->get_suffix();  }
    public function get_first_initial(){    return $this->first     ? strtoupper(substr($this->first,0,1)) . '. ' : ''; }
    public function get_middle_initial(){   return $this->middle    ? strtoupper(substr($this->middle,0,1)). '. ' : ''; }
    public function get_last_initial(){     return $this->last      ? strtoupper(substr($this->last,0,1))  . '. ' : ''; }
    public function get_nickname(){         return $this->nickname  ? '"'.$this->nickname.'"' : ''; }
}