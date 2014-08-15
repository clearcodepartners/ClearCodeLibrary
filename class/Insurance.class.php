<?php
/**
 * Insurance
 *
 * All of the information associated with a single insurance
 *
 * @property int|null ID
 * @property string company
 * @property Phone phone
 * @property string policy_number
 * @property Person policy_holder
 * @property string group
 * @property string member_id
 * @property string deductable
 * @package ClearCode\Insurance
 */
    class Insurance extends Lib {
        public function fresh(){
            $this->company = "";
            $this->phone = new Phone();
            $this->policy_number = "";
            $this->policy_holder = new Person();
            $this->group = "";
            $this->member_id = "";
            $this->deductable = "";
        }
        public function __toString(){
            if(!$this->policy_number) return "N/A";
            return ($this->policy_holder?$this->policy_holder."'s":""). ' policy at '.($this->company?:"Unknown") .' #'. $this->policy_number;
        }
    }