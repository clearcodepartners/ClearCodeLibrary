<?php

	/**
	 * HTML DOM Library
	 *
	 * A Library used to create HTML easily
	 * @package ClearCode\Library
	 */
	class Dom extends Lib {

		/**
		 * Elements that don't need closing element
		 * @access private
		 * @var string[] $singles
		 */
		private $singles = ['input', 'hr', 'br', 'link', 'img'];

		/**
		 * Type
		 *
		 * The current element type
		 *
		 * @var string $type
		 */
		public $type = '';

		/**
		 * Children
		 *
		 * The children of the DOM element
		 *
		 * @var Dom[] $children
		 */
		public $children = [];

		/**
		 * The current DOM element attributes
		 * @var array[] $attributes
		 */
		public $attributes = [];

        /**
         * @var array
         */
        private $attr;

        /**
         * @var string
         */
        private $text;

        /**
         * Make new Dom
         *
         * @param string $type Type
         * @param array $attr Attributes
         * @param string $text Content
         * @return Dom
         */
		function __construct($type, $attr = [], $text = ""){ $this->load_defaults(); $this->type 		= $type; $this->children 	= $text ? [$text] : []; if(!empty($attr)) foreach($attr as $k => $att){ if(is_numeric($k)) $this->attr($att); else($this->attr($k, $att)); } $this->attr = $attr; $this->text = $text; }

        /**
         * Set Attribute
         * Either: attr("Name","Value")
         * Or: attr(["Name", "Value"], ["Name","Value"])
         * Or: attr([["Name", "Value"], ["Name","Value"]])
         * @internal param mixed $args The attributes to set
         * @return array
         */
		function attr(){ $args = func_get_args(); if(!is_array($args[0]) && empty($args[1])) return $this->attributes[$args[0]]; else if(!is_array($args[0])) $this->attributes[$args[0]] = $args[1]; else foreach($args as list($k, $v)) $this->attributes[$k] = $v; }

		/**
		 * Prepend New Child Element
		 *
		 * @param string $type Type
		 * @param array $attr Attributes
		 * @param string $text Content
		 * @return Dom Child Element
		 */
		function &prepend($type, $attr = [], $text = null){ array_unshift($this->children, ""); $this->children[0] = new Dom($type, $attr, $text); return $this->children[0]; }

		/**
		 * Append New Child Element
		 *
		 * @param string $type Type
		 * @param array $attr Attributes
		 * @param string $text Content
		 * @return Dom Child Element
		 */
		function &append($type, $attr = [], $text = null){ $this->children[] = new Dom($type, $attr, $text); return $this->children[count($this->children) - 1]; }

		/**
		 * Append Text
		 *
		 * @param string $text Content
		 * @return void
		 */
		function append_text($text){ array_unshift($this->children, $text); }

		/**
		 * Prepend Text
		 *
		 * @param string $text Content
		 * @return void
		 */
		function prepend_text($text){ array_push($this->children, $text); }

		/**
		 * Output Dom
		 * @return string
		 */
		function __toString(){ $r = "<{$this->type} "; foreach($this->attributes as $k => $v) $r .= " {$k} = \"".addslashes($v)."\" "; $r .= " >"; if(!in_array($this->type, $this->singles)){ foreach($this->children as $child) $r .= (string) $child; $r .= "</{$this->type}>"; } return $r; }
	}