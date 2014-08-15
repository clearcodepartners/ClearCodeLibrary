<?php

/**
 * @property Category Sunday
 * @property Category Monday
 * @property Category Tuesday
 * @property Category Wednesday
 * @property Category Thursday
 * @property Category Friday
 * @property Category Saturday
 */
class Schedule extends Lib{
    public function fresh(){
        $this->Sunday       = new Category('Event');
        $this->Monday       = new Category('Event');
        $this->Tuesday      = new Category('Event');
        $this->Wednesday    = new Category('Event');
        $this->Thursday     = new Category('Event');
        $this->Friday       = new Category('Event');
        $this->Saturday     = new Category('Event');
    }
}