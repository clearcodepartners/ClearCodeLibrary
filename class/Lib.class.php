<?php
/**
 * Master Library
 * Methods all classes should have
 *
 */
	class Lib implements Iterator{
        /**
         * @var int
         */
        public      $id = null;
        /**
         * @var bool
         */
        private     $DEBUG = false;
        /**
         * @var Database
         */
        protected   $db;
        /**
         * @var MetaData
         */
        public      $metadata = null;
        /**
         * @var User
         */
        public      $current_user;
        /**
         * @var bool
         */
        public      $debug = false;
        /**
         * @var array
         */
        public      $config = [];
        /**
         * @var array
         */
        public      $sleep_arr = ['id', 'current_type'];
        /**
         * @var array
         */
        public      $params = [];
        /**
         * @var string
         */
        public      $current_type = "";
        /**
         * @var bool
         */
        public      $empty = false;
        /**
         * @var int
         */
        private     $position = 0;
        /**
         * @var array
         */
        private     $loops = [];
        /**
         * @var array
         */
        private     $loops_keys = [];
        /**
         * @var bool
         */
        public      $is_category = false;
        /**
         * @var array
         */
        public      $iterate_filters = [];

        /**
         * Serialize and Base64 for storage in the MetaData Database
         * @param $i
         * @return string
         */
        public function serialize($i){ if(is_array($i) || is_object($i)) $r = serialize($i); else $r = $i; return base64_encode($r); }

        /**
         * Is data from our database?
         * @param $str
         * @return bool
         */
        public function serialized($str) { return (base64_decode($str) == serialize(false) || @unserialize(base64_decode($str)) !== false); }

        /**
         * Unserialize and decode
         * @param $i
         * @return mixed|string
         */
        public function unserialize($i){ if($this->serialized($i)) return unserialize(base64_decode($i)); else return base64_decode($i); }

        /**
         * On Serialize, Cleanup and Return params to store
         * @return array
         */
        public function __sleep(){ return $this->sleep_arr; }

        /**
         * Wake Up and Load the full class
         */
        public function __wakeup(){ $this->load(); }

        /**
         * Define filters here
         */
        public function filters(){}

        /**
         * Stuff that should happen on load
         */
        public function onload(){}

        /**
         * Stuff that should happen before load
         */
        public function preload(){}

        /**
         * Stuff that should happen after load
         */
        public function postload(){}

        /**
         * Load
         */
        public function load(){
            $this->load_defaults();
            $this->preload();
            if($this->current_type == 'Database') return;
            if(!$this->id) {
                $this->id = $this->get_new_id();
                $this->load_metadata();
                $this->fresh();
            }
            else $this->load_metadata();
            $this->onload();
            $this->postload();
            if($this->current_type == 'Database') return;
            $this->build_loops();
        }

        /**
         * Get ID of a new instance
         * @param null $i
         * @return mixed
         */
        public function get_new_id($i = null){
            if($this->current_type == 'Database' || $this->current_type == 'MetaData') return;
            if($this->current_type == 'User'){
                return $this->id = $this->db->i(strtolower($this->current_type), ['username' => $this->username, 'deleted' => '0']);
            }
            return $this->id = $this->db->i(strtolower($this->current_type), ['deleted' => '0']);
        }

        /**
         * Start your engines
         */
        public function __construct(){
            $this->params = func_get_args();
            if(is_array($this->params[0])) $this->params = $this->params[0];
            if(is_numeric($this->params[0])) $this->id = $this->params[0];
            $this->load();
        }

        /**
         * Fields to setup when I make a new instance
         */
        public function fresh(){}

        /**
         * Delete all info related to this instance
         */
        public function delete(){
            foreach($this->metadata as $k => $v){
                unset($this->metadata->$k);
                $this->db->u(strtolower($this->current_type), ['deleted' => '1'], [['id', $this->id]]);
            }
        }

        /**
         * Set Data
         * @param $n
         * @param $v
         */
        public function __set($n, $v){ if($this->$n === $v) return; $this->metadata->$n = $v; }

        /**
         * Get Data
         * @param $n
         * @return null
         */
        public function __get($n){ return isset($this->metadata->$n)  ? $this->metadata->$n : null ; }

        /**
         * Isset Handler
         * @param $n
         * @return bool
         */
        public function __isset($n){ return isset($this->metadata->$n); }

        /**
         * Unset Handler
         * @param $n
         */
        public function __unset($n){ if(
            isset($this->metadata->$n) ) unset($this->metadata->$n);
        }

        /**
         * Call Handler
         *
         * What to do if a method is called that doesn't exist
         *
         * @param $n
         * @param $a
         */
        public function __call($n, $a){ /* Fizzle */ }

        /**
         * Invoke Handler
         *
         * What to do if this class is called as a function
         *
         * @return bool
         */
        public function __invoke(){ return $this->empty === true ? false : true;  }

        /**
         * Static Call Handler
         *
         * What to do if a static method is called that doesn't exist
         *
         * @param $n
         * @param $a
         */
        public static function __callStatic($n, $a){ /* Fizzle */ }

        /**
         * Load Default Information into class
         * * Config
         * * Current Type
         * * Debug Status
         * * Iterate Filters
         * * Database Class
         * * MetaData Class
         * @return void
         */
        public function load_defaults(){
            $this->config =& $GLOBALS['config'];
			$this->current_type = get_class($this);
			if(!empty($_REQUEST['debug'])) $this->debug(true);
            $this->iterate_filters[] = function($val){ return true; };
			if($this->current_type != 'Database'){
                $this->db = &load_db();
                if($this->current_type != 'MetaData' && $this->current_type != 'Auth') $this->db->q("CREATE TABLE IF NOT EXISTS clearcodelibrary.".strtolower($this->current_type)." ( id int(10) NOT NULL AUTO_INCREMENT, deleted tinyint(1) NOT NULL DEFAULT 0, changed_by int(10) NOT NULL, PRIMARY KEY (id), INDEX del (deleted) comment '', INDEX changed_by (changed_by) comment '' ) COMMENT='';");
            }
		}

        /**
         * Load Current User
         * @return void
         */
        public function load_user(){
            $this->current_user = &load_user();
        }

        /**
         * Load Metadata
         * @return void
         */
        public function load_metadata(){
            if($this->current_type == 'MetaData' || $this->current_type == 'Database' || $this->current_type == 'Auth' ||  $this->current_type == 'Dom') return;
            $this->metadata = new MetaData($this->id, $this->current_type);
            $this->filters();
        }

        /** Turn Debug On */
        public function debug_on(){ $this->debug(true); }

        /** Turn Debug off */
        public function debug_off() { $this->debug(false); }

        /**
         * Get/Set Debug Status
         *
         * @param null $p
         * @return bool
         */
        public function debug($p = null){ if(!is_null($p)) { $this->DEBUG = $p ? true : false; if(!empty($this->db)) $this->db->debug($this->DEBUG); } return $this->DEBUG; }

        /**
         * Get IP Address as Long
         * @return int
         */
        public function get_ip(){ return ip2long(!empty($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'] ));}

        /** Dump the content of this class and die */
        public function dump(){
            unset($this->db);
            unset($this->metadata->db);
            unset($this->config);
            dump($this);
        }

        /** Iterator: Build Loops */
        public function build_loops(){
            $i = 0;
            foreach($this->metadata as $k => $v){
                $pass = true;
                foreach($this->iterate_filters as $filter){
                    if(!$pass) continue;
                    if(!$filter($v)){
                        $pass = false;
                    }
                }
                if($pass){
                    $this->loops[$i] = $v;
                    $this->loops_keys[$i] = $k;
                    $i++;
                }
            }
        }

        /**
         * Char2Num
         * Convert PhoneNumber letter to number
         * @param $c
         * @return int|string
         */
        public function char2num($c){ if(is_numeric($c)) return $c * 1; $n = 2; foreach(range('a','z') as $i){ if(strtolower($c) == $i) return $n; if(in_array($i, ['c','f','i','l','o','s','v'])) $n++;} return 0; }

        /**
         * Chars2Num
         * Convert PhoneNumber letters to numbers
         * @param $int
         * @return string
         */
        public function chars2num($int){ if(is_numeric($int)) return $int; $str = (string) $int; $str2 = ""; $arr = str_split($str); foreach($arr as $char){ if(is_numeric($char)) $str2 .= $char; else $str2 .= $this->char2num($char); } return $str2; }

        /**
         * Zerofill
         * Prefix $int with zeroes until string is as wide as $chars
         * @param int $chars
         * @param int $int
         * @return string
         */
        public function zerofill($chars, $int){ $str = (string) $int; $len = strlen($str); for($i = $len; $i < $chars; $i++) $str = "0".$str; return $str; }

        /**
         * Truncate
         * @param int $len
         * @param string $str
         * @return string
         */
        public function truncate($len = 30, $str){
            $str = (string) $str;
            if(strlen($str) <= $len) return $str;
            return substr($str, 0, $len);
        }

        /** Iterator: Rewind */
        public function rewind(){ $this->position = 0; $this->build_loops(); }

        /**
         * Iterator: Current
         * @return mixed
         */
        public function current(){ return $this->loops[$this->position]; }

        /**
         * Iterator: Key
         * @return mixed
         */
        public function key(){ return $this->loops_keys[$this->position]; }

        /**
         * Iterator: Next
         */
        public function next(){ ++$this->position; }

        /**
         * Iterator: Valid
         * @return bool
         */
        public function valid(){ return isset($this->loops[$this->position]); }

        /*
        private $ui_template = "";
        private $fills = [];

        public function set_ui_template($str){ $this->ui_template = $str; }
        public function add_fills($default){ if(!is_array($default)) $this->fills[] = $default; else { foreach($default as $v) $this->fills[] = $v; } }
        public function get_ui(){
            $fills = func_get_args();
            foreach($this->func as $k => $v){ if(isset($fills[$k])) $this->fills[$k] = $fills[$k]; }
            return vsprintf($this->ui_template, $this->fills);
        }
        */
	}