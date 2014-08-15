<?php

/**
 * @property string name
 * @property Category addresses
 * @property Category phone_numbers
 * @property Category employees
 * @property Category email_addresses
 * @property Schedule schedule
 * @property string relation
 * @property Category drugs
 */
class Company extends Lib{
    public function fresh(){
        $this->name             = "";
        $this->addresses        = new Category('Address');
        $this->phone_numbers    = new Category('Phone');
        $this->employees        = new Category('Person');
        $this->email_addresses  = new Category('Email');
        $this->schedule         = new Schedule();
        $this->drugs            = new Category('Drug');
        $this->relation         = "";
    }
}