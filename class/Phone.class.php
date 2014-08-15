<?php
/**
 * Phone
 *
 * @property int country_code
 * @property int area_code
 * @property int prefix
 * @property int line_number
 * @property string|int extension
 */
class Phone extends Lib{
    public function fresh(){
        $this->country_code = 1;
        $this->area_code    = 0;
        $this->prefix       = 0;
        $this->line_number  = 0;
        $this->extension    = "";
    }
    public function importNumber($number){
        $cleaned = "";
        foreach($number as $k){
            if(is_numeric($k)) $cleaned .= $k;
            else if(in_array($k, range('a','z'))) $cleaned .= $this->char2num($k);
        }
        $retarr = explode("",$cleaned);
        if($retarr[0] != 1 || count($retarr) >= 11) $this->country_code     = 1;
        else $this->country_code                                            = array_shift($retarr);
        $this->area_code                                                    = array_shift($retarr)  .   array_shift($retarr)    .   array_shift($retarr);
        $this->prefix                                                       = array_shift($retarr)  .   array_shift($retarr)    .   array_shift($retarr);
        $this->line_number                                                  = array_shift($retarr)  .   array_shift($retarr)    .   array_shift($retarr)    .   array_shift($retarr);
        if(count($retarr) > 0 ) $this->extension                            = implode("", $retarr);
    }
    public function filters(){
        $this->metadata->add_set_filter('country_code', function($i){ return is_numeric($i)?(10>$i*1?$i*1:1):1; });
        $this->metadata->add_get_filter('country_code', function($i){ return is_numeric($i)?(10>$i*1?$i*1:1):1; });
        $this->metadata->add_set_filter('area_code',    function($i){ return $this->chars2num($i);              });
        $this->metadata->add_get_filter('area_code',    function($i){ return $this->zerofill( 3 , $this->truncate(3, $i) * 1 );     });
        $this->metadata->add_set_filter('prefix',       function($i){ return $this->chars2num($i);              });
        $this->metadata->add_get_filter('prefix',       function($i){ return $this->zerofill( 3 , $this->truncate(3, $i) * 1 );     });
        $this->metadata->add_set_filter('line_number',  function($i){ return $this->chars2num($i);              });
        $this->metadata->add_get_filter('line_number',  function($i){ return $this->zerofill( 4 , $this->truncate(4, $i) * 1 );     });
    }
    public function __toString(){ return "+" . $this->country_code . " (" . $this->area_code . ") " . $this->prefix . " - " . $this->line_number . ( $this->extension > 0 ? " x " . $this->extension : null); }
}