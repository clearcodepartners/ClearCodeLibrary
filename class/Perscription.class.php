<?php

/**
 * @property Drug drug
 * @property mixed regular
 * @property string date
 * @property string refills
 * @property string qty
 * @property string reason
 * @property string frequency
 * @property string delivery
 * @property string unit_form
 * @property string units
 * @property string unit_type
 */
class Perscription extends Lib{
    function fresh(){
        $this->drug         = new Drug($this->params[0]);
        $this->units        = "";        // 20
        $this->unit_type    = "";        // MG
        $this->unit_form    = "";        // tablets
        $this->delivery     = "";        // Mouth
        $this->frequency    = "";        // Twice Daily
        $this->reason       = "";        // ADHD
        $this->qty          = "";        // 60
        $this->refills      = "";        // 0
        $this->date         = "";
        $this->regular      = true;
    }
    function filters(){
        $this->metadata->add_set_filter('units',    function($s){ return is_numeric($s) ? $s : 0;       });
        $this->metadata->add_set_filter('qty',      function($s){ return is_numeric($s) ? $s : 0;       });
        $this->metadata->add_set_filter('refills',  function($s){ return is_numeric($s) ? $s : 0;       });
        $this->metadata->add_set_filter('regular',  function($s){ return ($s == true);                  });
        $this->metadata->add_set_filter('date',     function($s){ return date('m/d/Y', strtotime($s));  });
    }
}