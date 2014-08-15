<?php

/**
 * Class Person
 * @property Name name
 * @property Category addresses
 * @property Category phone_numbers
 * @property Category companies
 * @property Category email_addresses
 * @property Social ssn
 * @property String gender
 * @property String occupation
 * @property String relation
 */
class Person extends Lib{
    public function filters(){
        $this->metadata->add_set_filter('gender',function($s){ $ss = strtolower(substr($s, 0, 1)); return $ss == 'm' ? 'Male' : ( $ss == 'f' ? 'Female' : '' ); });
    }
    public function fresh(){
        $this->name             = new Name();
        $this->addresses        = new Category('Address');
        $this->phone_numbers    = new Category('Phone');
        $this->companies        = new Category('Company');
        $this->email_addresses  = new Category('Email');
        $this->ssn              = new Social();
        $this->gender           = "";
        $this->occupation       = "";
        $this->relation         = "";
    }
}