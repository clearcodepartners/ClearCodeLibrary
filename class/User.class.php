<?php
/**
 * User
 *
 * @property string     password
 * @property int        user_level
 * @property Person     person
 * @property Company    company
 * @property Category   survey
 */
	class User extends Lib{
        public  $id = null;
        public  $username = "";
		private $permissions = [];
        private $new = false;
		public  function preload(){
            $this->load_defaults();
            $this->username = null;
            $this->id = $this->params[0];
            $password = $this->params[1];
            if(!is_numeric($this->id)){
                $this->username = $this->id;
                $this->id = get_userid($this->username);
                if(!$password) trigger_error('No password supplied during user creation', E_USER_ERROR);
                else $this->new = $password;
            }
            else $this->username = $this->db->select_var('user', 'username', [['id', $this->id]]);
        }
        public  function filters(){
            $this->metadata->add_set_filter('password' , function($s){ return md5($s); } );
            $this->metadata->add_get_filter('status' , function($s){ return $s !== null ? $s : 0; } );
        }
        public  function fresh(){
            $this->password             = $this->new;
            $this->user_level           = 0;
            $this->person               = new Person();
            $this->company              = new Company();
            $this->survey               = new Category('Question');
        }
        public  function load_perms(){  $this->permissions          = $this->config['status_arr'][$this->user_level ? $this->user_level : 0]; }
        public  function can($perm){    $this->load_perms(); return $this->permissions[$perm] == true; }
        public  function is_admin(){    return $this->user_level == 20; }
    }