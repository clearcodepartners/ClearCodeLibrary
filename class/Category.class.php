<?php
    /**
     * Category
     *
     * @property string cat_type
     */
	class Category extends Lib{
        public $category_type;
        function onload(){
            $this->sleep_arr[] = "category_type";
            if($this->cat_type) $this->category_type = $this->cat_type;
            $this->is_category = true;
            $this->iterate_filters[] = function($val){ if(is_a($val, $this->category_type)) return true; return false;};
        }
        function fresh(){
            $this->category_type = $this->params[0];
            $this->cat_type = $this->params[0];
        }
        function gen_key($key){
            $base_key = $key ? $key : $this->config['untitled_prefix'];
            $i = 1;
            $key = $base_key;
            while(isset($this->metadata->$key)) $key = $base_key . "_" . ++$i;
            return $key;
        }
        function add($key = null, $params = []){
            $key = $this->gen_key($key);
            $class = $this->category_type;
            $this->$key = new $class($key, $params);
            return $key;
        }
        function move($current, $new){
            $id = $this->$current->id;
            $type = $this->$current->current_type;
            unset($this->$current);
            $new = $this->gen_key($new);
            $this->$new = new $type($id);
            return $new;
        }
        function remove($current){ unset($this->$current); }
	}