<?php

/**
 * Question
 *
 * @property bool use
 * @property int frequency
 * @property int time
 * @property string unit
 * @property string notes
 */
class Question extends Lib{

    private $unit_info = [];

    function preload(){
        $this->unit_info = [
            'second'    => 1,
            'minute'    => 60,
            'hour'      => 60*60,
            'day'       => 24*60*60,
            'week'      => 7*24*60*60,
            'month'     => 30*24*60*60,
            'year'      => 365*24*60*60
        ];
    }

    function filters(){
        $this->metadata->add_set_filter('use' ,         function($s){ return $s ? true : false; } );
        $this->metadata->add_get_filter('use' ,         function($s){ return $s ? true : false; } );

        $this->metadata->add_set_filter('frequency' ,   function($s){ return is_numeric($s) ? $s : 0; } );
        $this->metadata->add_get_filter('frequency' ,   function($s){ return is_numeric($s) ? $s : 0; } );

        $this->metadata->add_set_filter('time',         function($s){ return is_numeric($s) ? $s : 0; } );
        $this->metadata->add_get_filter('time',         function($s){ return is_numeric($s) ? $s : 0; } );

        $this->metadata->add_set_filter('unit',         function($s){ return isset($this->unit_info[$s]) ? $s : 'week'; } );
        $this->metadata->add_get_filter('unit',         function($s){ return isset($this->unit_info[$s]) ? $s : 'week'; } );

    }

    function convert_to($time = 1, $unit = 'week'){ $time = is_numeric($time) ? $time : 1; $unit = isset($this->unit_info[$unit]) ? $unit : $this->unit; $base = $this->frequency / ( $this->time * $this->unit_info[$this->unit] ); return $base * ( $time * $this->unit_info[$unit] ); }

    function fresh(){
        $this->use          = false;
        $this->frequency    = 0;
        $this->time         = 1;
        $this->unit         = 'week';
        $this->notes        = "";
    }

}