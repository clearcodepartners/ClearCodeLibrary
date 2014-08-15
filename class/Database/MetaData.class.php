<?php
	/**
	 * MetaData Class
	 */
	class MetaData extends Lib{
        /**
         * @var string
         */
        private $parent_type = '';
        /**
         * @var int
         */
        private $parent_id = 0;
        /**
         * @var Closure[]
         */
        private $set_filters = [];
        /**
         * @var Closure[]
         */
        private $get_filters = [];

        /**
         * @param int $id
         * @param string $type
         */
        function __construct($id, $type){ $this->load_defaults(); if(!$id) trigger_error('No ID Given for MetaData', E_USER_ERROR); if(!$type) trigger_error('No type Given for MetaData', E_USER_ERROR); global $metadata_info; if(!isset($metadata_info[$type])) $metadata_info[$type] = []; if(!isset($metadata_info[$type][$id])) $metadata_info[$type][$id] = [ 'info' => [], 'ids' => [], 'dates' => []]; $this->parent_type = $type; $this->set_parent_id($id); $this->build_loops(); }

		/**
		 * "Magic" set Handler
		 *
		 * @param string $n Name
		 * @param mixed $v Value
		 * @return void
		 */
		function __set($n, $v){ global $metadata_info; $ov = $metadata_info[$this->parent_type][$this->parent_id]['info'][$n]; $v = $this->do_set_filters($n, $v); if( $v === false || $v === $ov ) return; $metadata_info[$this->parent_type][$this->parent_id]['info'][$n] = $v; if(array_key_exists($n, $metadata_info[$this->parent_type][$this->parent_id]['ids'])) $this->db->update('metadata', ['deleted' => 1], [['id', $metadata_info[$this->parent_type][$this->parent_id]['ids'][$n]]]); $metadata_info[$this->parent_type][$this->parent_id]['ids'][$n] = $this->db->insert('metadata', [ 'type' => $n, 'content_id' => $this->set_data($v), 'parent_type' => $this->parent_type, 'parent_id' => $this->parent_id, 'changed_by' => ($_SESSION['login'] ?: 0) ]); $metadata_info[$this->parent_type][$this->parent_id]['dates'][$n] = strtotime($this->db->select_var('metadata', 'update_date', [['id', $metadata_info[$this->parent_type][$this->parent_id]['ids'][$n]]])); }

        /**
         * Handles inserting the content of data into the data table
         *
         * @param mixed $data Data to insert
         * @return int content_id
         *
         */
        function set_data($data){ $content = $this->serialize($data); $md5 = md5($content); $content_id = $this->db->select_var('data','id',[['md5', $md5]]); if(!is_numeric($content_id)) $content_id = $this->db->i('data',['data' => $content, 'md5' => $md5]); return $content_id; }

		/**
		 * "Magic" get Handler
		 *
		 * @param string $n Name
		 * @return mixed Value
		 */
		function __get($n){ global $metadata_info; return $this->do_get_filters($n, null) !== null || array_key_exists($n, $metadata_info[$this->parent_type][$this->parent_id]['info']) ? $this->do_get_filters($n, $metadata_info[$this->parent_type][$this->parent_id]['info'][$n]) : null; }

		/**
		 * "Magic" isset Handler
		 *
		 * @param string $n Name
		 * @return bool
		 */
		function __isset($n){ global $metadata_info; return array_key_exists($n, $metadata_info[$this->parent_type][$this->parent_id]['info']) ? true : $this->do_get_filters($n, null) !== null; }

		/**
		 * "Magic" unset Handler
		 *
		 * @param string $n Name
		 * @return void;
		 */
		function __unset($n){ global $metadata_info; if(array_key_exists($n, $metadata_info[$this->parent_type][$this->parent_id]['ids'])) $this->db->update('metadata', ['deleted' => 1], [['id', $metadata_info[$this->parent_type][$this->parent_id]['ids'][$n] ]]); unset($metadata_info[$this->parent_type][$this->parent_id]['info'][$n], $metadata_info[$this->parent_type][$this->parent_id]['ids'][$n], $metadata_info[$this->parent_type][$this->parent_id]['dates'][$n]); }

		/**
		 * Set Parent Id
		 *
		 * @param int $id Id
		 * @return void
		 */
		function set_parent_id($id){ if(!$id) return; global $metadata_info; $old = $this->parent_id; $this->parent_id = $id; if($old){ $metadata_info[$this->parent_type][$this->parent_id] = $metadata_info[$this->parent_type][$old]; unset($metadata_info[$this->parent_type][$old]); } else { $raw_info = $this->db->select_assoc('metadata', ['*'], [['parent_type', $this->parent_type],['parent_id', $this->parent_id], ['deleted', 0]]); if($raw_info != false) foreach((array)$raw_info as $val) $this->set_info($val['type'], $this->db->select_var('data', 'data', [['id', $val['content_id']]]), $val['id'], $val['update_date'] ); } }

        /**
         * Set Info
         * @param $type
         * @param $content
         * @param $id
         * @param $date
         */
        private function set_info($type, $content, $id, $date){ if(!$type) trigger_error("No type set for metadata", E_USER_ERROR); global $metadata_info; $metadata_info[$this->parent_type][$this->parent_id]['info'][$type] = $this->unserialize($content); $metadata_info[$this->parent_type][$this->parent_id]['ids'][$type] = $id; $metadata_info[$this->parent_type][$this->parent_id]['date'][$type] = strtotime($date); }

        /**
         * Add Set Filter
         * @param string $name
         * @param Closure $filter
         * @param int $priority
         * @return bool
         */
        public function add_set_filter($name = '', $filter, $priority = 10){ if(!isset($this->set_filters[$name])) $this->set_filters[$name] = []; if(!isset($this->set_filters[$name][$priority])) $this->set_filters[$name][$priority] = []; $this->set_filters[$name][$priority][] =& $filter; return true; }

        /**
         * Add Get Filter
         * @param string $name
         * @param Closure $filter
         * @param int $priority
         * @return bool
         */
        public function add_get_filter($name = '',  $filter, $priority = 10){ if(!isset($this->get_filters[$name])) $this->get_filters[$name] = []; if(!isset($this->get_filters[$name][$priority])) $this->get_filters[$name][$priority] = []; $this->get_filters[$name][$priority][] =& $filter; return true; }

        /**
         * Do Set Filters
         * @param string $name
         * @param mixed $val
         * @return mixed
         */
        private function do_set_filters($name = '', $val){ if(!isset($this->set_filters[$name])) return $val; foreach($this->set_filters[$name] as $priority => $filters) foreach($filters as $filter) $val = $filter($val); return $val; }

        /**
         * Do Get Filters
         * @param string $name
         * @param mixed $val
         * @return mixed
         */
        private function do_get_filters($name = '', $val){ if(!isset($this->get_filters[$name])) return $val; foreach($this->get_filters[$name] as $priority => $filters) foreach($filters as $filter) $val = $filter($val); return $val; }

        /**
         * @var int
         */
        private $position = 0;
        /**
         * @var array
         */
        private $loops = [];
        /**
         * @var array
         */
        private $loops_keys = [];

        /**
         * Iterator: Build Loops
         */
        function build_loops(){ global $metadata_info; $i = 0; foreach((array)$metadata_info[$this->parent_type][$this->parent_id]['info'] as $k => $v){ $this->loops[$i] = $this->$k; $this->loops_keys[$i] = $k; $i++; } }

        /**
         * Iterator: Rewind
         */
        function rewind(){ $this->position = 0; $this->build_loops(); }

        /**
         * Iterator: Current
         * @return mixed
         */
        function current(){ return $this->loops[$this->position]; }

        /**
         * Iterator: Mixed
         * @return mixed
         */
        function key(){ return $this->loops_keys[$this->position]; }

        /**
         * Iterator: Next
         */
        function next(){ ++$this->position; }

        /**
         * Iterator: Valid
         * @return bool
         */
        function valid(){ return isset($this->loops[$this->position]); }
	}