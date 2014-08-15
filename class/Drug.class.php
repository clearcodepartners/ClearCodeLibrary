<?php

/**
 * @property string name
 * @property string application_number
 * @property string market_status
 * @property string tecode
 * @property string refrence_drug
 * @property string active_ingredient
 * @property string application_type
 * @property string sponsor_applicant
 * @property string most_recent_label
 * @property string current_patient_flag
 * @property string action_type
 * @property string chemical_type
 * @property string therapeutic_potential
 * @property string doc_type
 * @property string doc_title
 * @property string doc_url
 * @property string doc_date
 * @property string duplicate_counter
 */
class Drug extends Lib{
    function fresh(){
        $this->name                     = "";
        $this->application_number       = "";
        $this->market_status            = "";
        $this->tecode                   = "";
        $this->refrence_drug            = "";
        $this->active_ingredient        = "";
        $this->application_type         = "";
        $this->sponsor_applicant        = "";
        $this->most_recent_label        = "";
        $this->current_patient_flag     = "";
        $this->action_type              = "";
        $this->chemical_type            = "";
        $this->therapeutic_potential    = "";
        $this->doc_type                 = "";
        $this->doc_title                = "";
        $this->doc_url                  = "";
        $this->doc_date                 = "";
        $this->duplicate_counter        = "";
    }
}