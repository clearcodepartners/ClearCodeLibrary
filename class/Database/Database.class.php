<?php
/**
 * The Database Interface Library
 * @package ClearCode\Database
 *
 * @todo
 * * Add "Add Table", "Edit Table", and "Drop Table"
 * * Add "Add Column", "Edit Column", and "Drop Column"
 * *
 */
	class Database extends Lib{

		/**
		 * Is Currently Connected
         *
		 * @var bool $connected
		 */
		public $connected = false;

        /**
         * Use Cache
         *
         * @var bool
         */
        private $use_cache = true;

		/**
		 * Query Cache
         *
		 * @var array[] $cache Cache
		 */
		private $cache = array();

		/**
		 * Database Link
         *
		 * @var resource $link Link
		 */
		public $link;

		/**
		 * Table Alias Array
         *
		 * @access private
		 * @var array $table_alias
		 */
		private $table_alias = [
			'users' => 'user'
		];

		/**
		 * Queue Status
         *
		 * @access private
		 * @var bool $queue_status
		 */
		private $queue_status = false;

		/**
		 * Query Queue
         *
		 * @access private
		 * @var array[] $queue_array
		 */

		private $queue_array = [];


        /**
         * Settings
         * @var array
         */
        private $settings = [];

		/**
		 * Queue
         *
		 * @access public
		 * @param boolean|null $status Status (null returns current, boolean set)
		 * @return boolean Current Status
		 */
		public function queue($status = null){ if($status === $this->queue_status) return; if($status === true) { $this->queue_status = true; return true; } if($status === false){ $this->queue_status = false; $this->process_queue(); return false; } return $this->queue_status; }

		/**
		 * Start Queue
         *
		 * @see Database::queue()
		 * @return bool Current Status
		 */

		public function start_queue(){ return $this->queue(true); }

		/**
		 * End Queue
         *
		 * @see Database::queue()
		 * @return bool Current Status
		 */

		public function end_queue(){ return $this->queue(false); }

		/**
		 * Process Queue
         *
		 * @return mixed[] An array of the queued database results, in order
		 */
		public function process_queue(){ $oq = $this->queue(); $this->queue(false); $returns = []; foreach($this->queue_array as $k => list($query, $type, $col)){ $returns[] = $this->q($query, $type, $col); unset($this->queue_array[$k]); } $this->queue($oq); return $returns; }

		/**
		 * Clear Queue w/o Processing
         *
		 * @return void
		 */
		public function clear_queue(){ $this->queue_array = []; }

		/**
		 * Database Name
         *
         * @var string
		 */
		private $db_name = "";

		/**
		 * Connect to database
		 *
		 * @param string $host host (IP/URL/localhost)
		 * @param string $user Username
		 * @param string $pass Password
		 * @param string $db Database Name
		 * @return void
		 */
		public function connect($host, $user, $pass, $db){ $this->db_name = $db; $this->load_defaults(); $args = func_get_args(); foreach($args as $arg){ if(!$arg) die("Missing DB info."); } $this->settings = array('host' => $host, 'user' => $user, 'pass' => $pass); $this->link = mysql_connect($host, $user, $pass) or die ("Failed to connect to database server"); mysql_select_db($db, $this->link) or die("Failed to connect to database"); $this->connected = true; $this->build_tables(); $this->tables = $this->get_tables(); }

        /**
         * Build Default Tables
         *
         * @return void
         */
        private function build_tables(){ $this->q("CREATE TABLE IF NOT EXISTS metadata ( id int(11) NOT NULL AUTO_INCREMENT, type char(20) NOT NULL, parent_id int(10) NOT NULL, parent_type char(20) NOT NULL, content_id int(11) NOT NULL, update_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, deleted tinyint(1) NOT NULL DEFAULT '0', changed_by int(10) NOT NULL DEFAULT '0', PRIMARY KEY (id), KEY parent_id (parent_id), KEY type (type), KEY parent_type (parent_type), KEY deleted (deleted), KEY update_date (update_date), KEY changed_by (changed_by) USING BTREE ) "); $this->q("CREATE TABLE IF NOT EXISTS data ( id int(11) NOT NULL AUTO_INCREMENT, data blob, md5 char(32) DEFAULT NULL, PRIMARY KEY (id), KEY md5 (md5) ) "); $this->q("CREATE TABLE IF NOT EXISTS auth_log ( id int(11) NOT NULL AUTO_INCREMENT, timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ip INT(11) UNSIGNED DEFAULT '0', msg char(255) DEFAULT NULL, user_id int(11) NOT NULL DEFAULT '0', status tinyint(1) NOT NULL DEFAULT '0', PRIMARY KEY (id), KEY timestamp (timestamp), KEY user_id (user_id), KEY ip (ip) ) "); $this->q("CREATE TABLE IF NOT EXISTS login_log ( id int(11) NOT NULL AUTO_INCREMENT, username char(255) DEFAULT NULL, password char(32) DEFAULT NULL, ip INT(11) UNSIGNED DEFAULT '0', timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, deleted tinyint(1) NOT NULL DEFAULT '0', PRIMARY KEY (id))"); $this->q("CREATE TABLE IF NOT EXISTS user ( id int(11) NOT NULL AUTO_INCREMENT, username varchar(50) NOT NULL, deleted tinyint(1) NOT NULL DEFAULT 0, changed_by int(10) NOT NULL, PRIMARY KEY (id), KEY deleted (deleted), KEY changed_by (changed_by) ) "); }

		/**
		 * Disconnect from Database
		 */
		public function disconnect(){ if($this->connected == false) return; if($this->queue()) $this->end_queue(); mysql_close($this->link); $this->connected = false; }

		/**
		 * Destructor
		 *
		 * @uses Database::disconnect() to close the database connection
		 * @return void
		 */
		public function __destruct(){ $this->disconnect(); }

        /**
         * Clear Cache
         */
        public function clear_cache(){ $this->cache = []; }

        /**
         * Construct
         */
        public function __construct(){}

		/**
		 * Get Primary Key of Table
         *
		 * @param string $table Table
		 * @return string Primary Key Column Name
		 */
		public function get_primary_key($table){ return $this->q("SHOW KEYS FROM ".$this->table($table)." WHERE Key_name = 'PRIMARY'",'v','Column_name'); }

		/**
		 * Run Query
		 *
		 * * Runs a MySQL Query on the database.
		 * @param string $q Mysql Query to Run
		 * @param string $type Type of Return
		 * * 'a',   'assoc'          : Returns an Associative Array of Associative Arrays where the Key value of the outside array is equal too $col
		 * * 'n',   'numeric         : Returns an Array of Numeric Arrays
		 * * 'sa',  'single_assoc'   : Returns a Single Associative Array
		 * * 'sn',  'single_numeric  : Returns a Single Numeric Array
		 * * 'v',   'value'          : Returns a Single Value from the column set with $col from the first result row found
		 * * 'i',   'insert'         : Use only for Insert statements.  Returns the Insert Id
		 * * 'u',   'update'         : Use only for Update statements.  Returns the number of results updated.
		 * * 'd',   'delete'         : Use only for Delete statements. Returns the number of rows deleted.
		 *
		 * @param string $col  Column modifier specific to type
		 * @param bool $silent
		 * @return mixed formatted return type
		 */
		public function q($q, $type = '', $col = '', $silent = false){ $a = explode(' ',$q, 1); if(strtolower($a[0]) == 'update' || strtolower($a[0]) == 'insert' || strtolower($a[0]) == 'delete') $this->cache = array(); unset($a); if($this->queue()){ $this->queue_array[] = [$q, $type, $col]; return null; } if($type == 'r'){ $r = mysql_query($q, $this->link); if($this->err()) die( (string) new Dom('div', ['class' => 'error'], $this->err() . "<br>" . $q ) ); return $r; } $return_a_arr = $return_n_arr = array(); if(!empty($this->cache[md5($q)]) && $this->use_cache) $return_a_arr = $this->cache[md5($q)]; else { $result = mysql_query($q, $this->link); if($this->err()){ $this->cache[md5($q)] = false; if($this->debug()) echo new Dom('div', ['class' => 'error'], $this->err() . "<br>" . $q); return false; } if(!is_bool($result)){ while($r = mysql_fetch_assoc($result)) $return_a_arr[] = $r; foreach($return_a_arr as $i => $r){ if(array_key_exists('deleted', $r) && $r['deleted'] == 1) unset($r[$i]); } if(count($return_a_arr) == 0){ $this->cache[md5($q)] = false; return false; } $this->cache[md5($q)] = $return_a_arr; } } if($type == 'n' || $type == 'sn'){ foreach($return_a_arr as $k => $v){ $ctr = 0; $arr = array(); foreach($v as $k2 => $v2){ $arr[$ctr] = $v2; $ctr++; } $return_n_arr[] = $arr; } } switch($type){ case 'array': case 'a': $return = $return_a_arr; break; case 'numeric': case 'n': $return = $return_n_arr; break; case 'singlearray': case 'sa': if(count($return_a_arr) == 0) $return = array(); else $return = array_shift($return_a_arr); break; case 'singlenumeric': case 'sn': if(count($return_n_arr) == 0) $return = array(); else $return = array_shift($return_n_arr); break; case 'var': case 'singlevar': case 'v': if(count($return_a_arr) == 0) $return = false; else{ $r = array_shift($return_a_arr); $return = $r[$col] ? $r[$col] : false; } break; case 'i': case 'insert': $return = $this->insert_id(); break; case 'u': case 'update': case 'd': case 'delete': $return = true; break; default: $return = count($return_a_arr); break; } return $return; }

		/**
		 * Tables
         *
         * @var array[]
		 */
		private $tables = [];

		/**
		 * Get Tables
         *
		 * @return array[] TableName => Columns
		 */
		private function get_tables(){ $tbls_raw = $this->q("SHOW TABLES FROM {$this->db_name}", 'n'); $r = []; foreach($tbls_raw as list($tbl_nm)) $r[$tbl_nm] = $this->get_cols($tbl_nm); return $r; }

		/**
		 * Get Columns
         *
		 * @param string $table The table to get the columns from
		 * @return array[] Column Name => [ Type, Length, Null, Default ]
		 */
		private function get_cols($table){ $cols_raw = $this->q("SHOW COLUMNS FROM {$table}",'a'); $cols_final = []; foreach($cols_raw as $v){ list($type,$length) = explode("(", str_replace(")","",$v['Type'])); $cols_final[$v['Field']] = [ 'type' => $type, 'length' => $length, 'null' => $v['Null'] == "NO" ? false : true, 'default' => $v['Default'] ]; } return $cols_final; }

		/**
		 * Get Table Alias
         *
		 * @uses Database::$table_alias to check for any aliases to return
		 * @param string[] $as_arr
		 * @param string $n name
		 * @return string Alias
		 */
		private function table($n, $as_arr = []){ $aliases = array_merge($this->table_alias, $as_arr); return array_key_exists($n, $aliases) ? $aliases[$n] : $n; }

		/**
		 * Is Column Type Numeric
         *
		 * @param string $type Column Type
		 * @return bool
		 */
		private function is_numeric($type){ return in_array($type, ['int','double','tinyint','smallint','bigint', 'mediumint','integer','float','double precision','real','decimal','numeric']); }

		/**
		 * Is Column Type String
         *
		 * @param string $type Column Type
		 * @return bool
		 */
		private function is_string($type){ return in_array($type, ['char', 'varchar','text','blob','tinyblob','tinytext','mediumblob','mediumtext','longblob', 'longtext']); }

		/**
		 * Is Column Type Date or Time
         *
		 * @param string $type Column Type
		 * @return bool
		 */
		private function is_date($type){ return in_array($type, ['date','time','timestamp','datetime','year']); }

		/**
		 * Clean Mysql Value
         *
		 * @param string $table Table Name
		 * @param string $column Column Name
		 * @param string $value Raw Value
		 * @return string|bool Parsed Value
		 */
		private function clean_value($table, $column, $value){ if($this->tables[$this->table($table)] == null) return false; else if(!array_key_exists($column, $this->tables[$this->table($table)]))  return false; else if($this->is_numeric($this->tables[$this->table($table)][$column]['type'])) { if(!is_numeric($value)) return false; } else if($this->is_string($this->tables[$this->table($table)][$column]['type'])) { if(is_null($value)) return false; $value = '"'.mysql_real_escape_string($value, $this->link).'"'; } else if($this->is_date($this->tables[$this->table($table)][$column]['type'])) { if(strtotime($value) === false) return false; switch ($this->tables[$this->table($table)][$column]['type']){ case 'year': $value = '"'.date('Y', strtotime($value)).'"'; break; case 'date': $value = '"'.date('Y-m-d', strtotime($value)).'"'; break; case 'datetime': $value = '"'.date('Y-m-d H-i-s', strtotime($value)).'"'; break; case 'time': $value = '"'.date('H-i-s', strtotime($value)).'"'; break; case 'timestamp': $value = '"'.date('YmdHis', strtotime($value)).'"'; break; default: $value = false; }} if(!empty($this->tables[$this->table($table)][$column]['length']) && strlen($value) > $this->tables[$this->table($table)][$column]['length']){ if($this->is_string($this->tables[$this->table($table)][$column]['type'])) substr( $value, 0, $this->tables[$this->table($table)][$column]['length']); else return false; } return $value; }

		/**
		 * Make Insert String
         *
		 * @param string $table The table to insert too
		 * @param array $arr the Array of info to insert, where key is the column name
		 * @return string the insert query
		 */
		private function make_insert($table, $arr = []) { $table = $this->table($table); $cols = $vals = ""; foreach ($arr as $k => $v) { $v = $this->clean_value($table, $k, $v); if(!$v) continue; $cols .= ($cols ? ', ':'') . $k; $vals .= ($vals ? ', ':'') . $v; } return "INSERT INTO {$table} ({$cols}) VALUES ({$vals})"; }

        /**
         * Make Update String
         *
         * @param string $table The table to insert too
         * @param array $arr the data to update [ column => value ]
         * @param array|bool $where where info
         * @return string the insert query
         */
		private function make_update($table, $arr = [], $where = false){ $table = $this->table($table); $vs = ''; foreach((array) $arr as $k => $v){ $v = $this->clean_value($table, $k, $v); if(!$v) continue; $vs .= ($k != 'submit' ? ($vs ? ',' : '')."{$k}='{$v}'" : ""); } return "UPDATE {$table} SET {$vs} " . $this->where($where, $table); }

        /**
         * Make Delete String
         *
         * @param string $table The table to insert too
         * @param array|null $where
         * @return string the delete query
         */
		private function make_delete($table = '', $where = null){ $table = $this->table($table); return "DELETE FROM {$table}" . $this->where($where, $table); }

		/**
		 * Get Insert Id
		 *
		 * @return int The Insert Id
		 */
		public function insert_id(){ return mysql_insert_id($this->link); }

		/**
		 * Get Mysql Error
		 *
		 * @return string mysql_error
		 */
		public function err(){ return mysql_error($this->link); }

		/**
		 * Do Database Insert
		 *
		 * @param string $table The table to insert too
		 * @param array $arr the Array of info to insert, where key is the column name
		 * @return mixed insert_id on success, FALSE on fail
		 */
		public function insert($table, $arr = array()){ return $this->q($this->make_insert($this->table($table), $arr), 'i'); }

		/**
		 * Do Database Insert
		 *
		 * @param string $table The table to insert too
		 * @param array $arr the Array of info to insert, where key is the column name
		 * @return mixed insert_id on success, FALSE on fail
		 */
		public function i ($table, $arr = []){ return $this->insert($table, $arr); }

        /**
         * Do Database Update
         *
         * @param string $table The table to update
         * @param array $arr the data to update [ column => value ]
         * @param bool|mixed[] $where {@see Database::where()}
         * @return bool
         */
		public function update( $table, $arr = [], $where = false ){ return $this->q($this->make_update($this->table($table), $arr, $where), 'u'); }

        /**
         * Do Database Update
         *
         * @param string $table The table to update
         * @param array $arr the data to update [ column => value ]
         * @param bool|mixed[] $where {@see Database::where()}
         * @return bool
         */
		public function u( $table, $arr = [], $where = false){ $this->update($table, $arr, $where);}

        /**
         * Do Database Delete
         *
         * @param string $table The table to delete from
         * @param bool|mixed[] $where {@see Database::where()}
         * @return bool
         */
		public function delete($table, $where = false){ return $this->q($this->make_delete($this->table($table), $where), 'd'); }

        /**
         * Do Database Delete
         *
         * @param string $table The table to delete from
         * @param bool|mixed[] $where {@see Database::where()}
         * @return bool
         */
		public function d($table, $where = false){ $this->delete($table, $where); }

        /**
         * Do Select
         *
         * @see Database::where() for $where syntax
         * @see Database::q() for $type syntax
         * @todo Add "as" support
         * @todo Add "join" support
         * @param string|string[]|array[] $table Table Name
         * @param mixed[] $fields Fields
         * @param mixed[] $where Where Information
         * @param string $type Type of Data to return
         * @return mixed
         */
		public function s($table, $fields = ["*"], $where = [], $type = 'a'){ $q = "SELECT "; $fs = ""; $as_arr = []; if(!$fields) $fields = ["*"]; foreach((array)$fields as $field){ if(is_array($field)) list($field, $field_as) = $field; list($t, $field) = explode('.', $field); if(empty($field)){ $field = $t; $t = $table; } if($field != "*" && !isset($this->tables[$this->table($t)][$field])) continue; $fs .= ( $fs == "" ? "" : ", ") . ($t != $table ? $this->table($t) . "." : "") . $field . ($field_as ? " as {$field_as} ":""); } $q .= $fs; $from_string = ""; if(is_array($table)){ if(!isset($this->tables[$this->table($table[1])])){ list($table, $table_as) = $table; if($table_as) $as_arr[$table_as] = $this->table($table); $from_string = $this->table($table) . ( $table_as ? " as ".$table_as : " " ); } else{ $first_table = ""; foreach($table as $tbl){ if(is_array($tbl)) list($table, $table_as) = $table; if(empty($first_table)) $first_table = $table; if($table_as) $as_arr[$table_as] = $this->table($table); $from_string .= (empty($from_string) ? "" : ", ") . $this->table($table) . ( $table_as ? " as ".$table_as : " " ); } $table = $first_table; } } else $from_string = $this->table($table); $q .= " FROM " . $from_string; if(!empty($where)) $q .=  $this->where($where, $table, $as_arr); return $this->q($q, $type, ($type == 'v' ? $fields[0] : null )); }

        /**
         * Build Where Conditions
         *
         * where_arr = [ 'Column' (required) , 'Value' (required) , 'Comparitor' (optional), 'Joiner' (optional) ]
         * Usage: [ ['id','1', '=', 'AND'], [ ['username', 'joe'], ['password','schmoe'], " OR "] ]
         * Result: `id` = '1' OR ( `username` = "joe" AND `password` = "schmoe" )
         *
         * @todo add support for table.col = table2.col
         * @param array[] $args A multidimentional array in a mix of these formats:
         * * [ where_arr,where_arr,... ]
         * * [ [ where_arr,where_arr,... 'Joiner' ], ...]
         * @param $table
         * @param array $as_arr
         * @return string Where String
         */
        private function where($args, $table = "", $as_arr = []){ if($args === false) return ''; $r = ''; $wctr = 0; foreach($args as $key => list($k, $v, $c, $j)){ if(is_array($k) && !is_string($k[0]) ) { $j = " AND "; if(is_string($args[$key][(count($args[$key]) - 1)])) $j = array_pop($args[$key]); $r .= ($wctr != 0 ? $j : "" )." ( ".$this->where($args[$key])." ) "; } else{ if(!$k) continue; if(!$c) $c = " = "; if(!$j) $j = " AND "; if(is_array($k)) list($k, $k_as) = $k; list($t, $k) = explode('.', $k); if(empty($k)){ $k = $t; $t = $table; } if(!isset($this->tables[$this->table($t, $as_arr)][$k])) continue; $e = explode('.', $v); if(count($e) == 2 && isset($this->tables[$this->table($e[0], $as_arr)][$e[1]])) $v = " {$e[0]}.{$e[1]} "; else if(!is_numeric($v)) $v = " \"{$v}\" "; else $v = mysql_real_escape_string($v, $this->link); $r .= ($wctr != 0 ? $j : "") . " ".($t != $table ? $this->table($t, $as_arr)."." : "")."{$k} " . $c . $v; } $wctr++; } return $wctr > 0 ? " WHERE " . $r : ""; }

		/**
		 * Select
		 *
		 * Alias for Database::s()
         *
		 * @uses Database::s() for everything;
		 * @see Database::where() for $where syntax
		 * @see Database::q() for $type syntax
		 * @param string $table Table Name
		 * @param array $fields Fields
		 * @param array $where Where Information
		 * @param string $type Type of Data to return
         * @return mixed
		 */
		public function select($table, $fields, $where, $type){ return $this->s($table, $fields, $where, $type); }

		/**
		 * Select Associative Array
		 *
		 * Runs a Select query against the database using Database::s() and returns an Associative Array
         *
		 * @uses Database::s() for everything;
		 * @see Database::where() for $where syntax
		 * @param string $table Table Name
		 * @param array $fields Fields
		 * @param array $where Where Information
		 * @return array Associative Array of Results
		 */
		public function select_assoc($table, $fields, $where){ return $this->s($table, $fields, $where, 'a'); }

		/**
		 * Select Numeric Array
		 *
		 * Runs a Select query against the database using Database::s() and returns a Numeric Array
         *
		 * @uses Database::s() for everything;
		 * @see Database::where() for $where syntax
		 * @param string $table Table Name
		 * @param array $fields Fields
		 * @param array $where Where Information
		 * @return array Numeric Array of Results
		 */
		public function select_numeric($table, $fields, $where){ return $this->s($table, $fields, $where, 'n'); }

		/**
		 * Select Single Associative Array
		 *
		 * Runs a Select query against the database using Database::s() and returns an Associative Array for a single result
         *
		 * @uses Database::s() for everything;
		 * @see Database::where() for $where syntax
		 * @param string $table Table Name
		 * @param array $fields Fields
		 * @param array $where Where Information
		 * @return array Associative Array of Single Result
		 */
		public function select_single_assoc($table, $fields, $where){ return $this->s($table, $fields, $where, 'sa'); }

		/**
		 * Select Single Numeric Array
		 *
		 * Runs a Select query against the database using Database::s() and returns a Numeric Array for a single result
         *
		 * @uses Database::s() for everything;
		 * @see Database::where() for $where syntax
		 * @param string $table Table Name
		 * @param array $fields Fields
		 * @param array $where Where Information
		 * @return array Numeric Array of Single Result
		 */
		public function select_single_numeric($table, $fields, $where){ return $this->s($table, $fields, $where, 'sn'); }

		/**
		 * Select Variable
         *
		 * Runs a Select query against the database using Database::s() and returns a single variable from a single result
         *
		 * @uses Database::s() for everything;
		 * @see Database::where() for $where syntax
		 * @param string $table Table Name
		 * @param array $field Field
		 * @param array $where Where Information
		 * @return mixed The variable to return
		 */
		public function select_var($table, $field, $where){ return $this->s($table, [$field], $where, 'v'); } 
	}