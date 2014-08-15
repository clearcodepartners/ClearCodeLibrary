<?php
/**
 * Email
 *
 * All of the information associated with a single Email
 *
 * @property string email
 */
class Email extends Lib{
    public function filters(){
        $this->metadata->add_set_filter('email', function ($s){ return filter_var($s, FILTER_VALIDATE_EMAIL) ? $s : false; });
    }
    public function fresh(){
        $this->email = "";
    }
    public function __toString(){
        return strtolower($this->email);
    }
}