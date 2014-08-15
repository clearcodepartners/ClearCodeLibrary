<?php

/**
 * @property string start_time
 * @property string end_time
 * @property mixed all_day
 * @property string title
 * @property string location
 * @property string description
 */
class Event extends Lib{
    public function fresh(){
        $this->start_time = "";
        $this->end_time = "";
        $this->all_day = false;
        $this->title = "";
        $this->location = "";
        $this->description = "";
    }
}