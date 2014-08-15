<?php
/**
 * File for the Address Class
 * @author Mike Flynn <mflynn@cngann.com>
 * @package ClearCode\Address
 */

/**
 * Address
 *
 * All of the information associated with a single address
 *
 * @property int|null ID
 * @property string street1
 * @property string street2
 * @property string city
 * @property string state
 * @property string zip
 * @property string zip_ext
 * @property string country
 * @package ClearCode\Address
 */
class Address extends Lib{
    public function onload(){
    }
    public function filters(){
        $this->metadata->add_set_filter('zip', function($s){ return $this->zerofill( 5, $this->truncate(5, $s) * 1); });
    }
    public function fresh() {
        $this->street1  = "";
        $this->street2  = "";
        $this->city     = "";
        $this->state    = "";
        $this->zip      = "";
        $this->zip_ext  = "";
        $this->country  = "";
    }
    public function __toString(){
        if(!$this->street1) return "N/A";

        $r   =  $this->street1 . "<br>";
        $r  .=  ( $this->street2    ? $this->street2 . "<br>"   : ""                         );
        $r  .=  $this->city         .   ', ';
        $r  .=  $this->state        .   ', ';
        $r  .=  $this->zip;
        $r  .=  ( $this->zip_ext    ? '-'.$this->zip_ext        : ""                         ) . '<br>';
        $r  .=  ( $this->country    ? $this->country            : $this->config['default_country'] );

        return $r;
    }
}