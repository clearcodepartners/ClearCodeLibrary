<?php

/**
 * @property string block1
 * @property string block2
 * @property string block3
 */
class Social extends Lib{
    public function filters(){
        $this->metadata->add_set_filter('block1', function($s){ return $this->zerofill( 3, $this->truncate(3, $s) * 1); });
        $this->metadata->add_set_filter('block2', function($s){ return $this->zerofill( 2, $this->truncate(2, $s) * 1); });
        $this->metadata->add_set_filter('block3', function($s){ return $this->zerofill( 4, $this->truncate(4, $s) * 1); });
    }
    public function fresh(){
        $this->block1 = "";
        $this->block2 = "";
        $this->block3 = "";
    }
    public function __toString(){ return $this->block1 . '-' . $this->block2 . '-' . $this->block3; }
}