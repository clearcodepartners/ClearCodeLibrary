<?php
/**
 * ClearCodeLibrary Testing Init Functions
 * @package ClearCode
 */

    /** Include Autoload Function */
    require_once('functions/__autoload.func.php');
    /** Include Dump Function */
    require_once('functions/dump.func.php');
    /** Include Load Database Function */
    require_once('functions/load_db.func.php');

    /** @var Database The current database class */
    $db = new Database();

    /** @var User[] All users currently loaded  */
    $users = [];

    /** @var User The current User */
    $user = false;
    /** @var Auth The main authorization object */
    $auth = new Auth();
    /** Load the User */
    $user = &load_user();

    /**
     * Load User
     * @param null|int $id The user ID.  Default: logged in User
     * @return User Current User
     */
    function &load_user($id = null){
        global $users;
        $current = null;
        if($id == null){
            if(empty($_SESSION['login'])) return false;
            $id = $_SESSION['login'];
            $current = true;
        }
        if(!array_key_exists($id, $users)){
            $the_user = new User($id);
            $users[$id] =& $the_user;
        }
        return $users[$id];
    }

    /**
     * Create New User
     *
     * Status codes on fail:
     * * 1 : missing username
     * * 2 : missing password
     * * 3 : username exists
     *
     * @param string $username Username
     * @param string $password Password
     * @return User|bool The new user or status code on failure
     */
    function &create_user($username, $password){
        global $users;
        if(!$username) return 1;
        if(!$password) return 2;
        $new_user = new User($username, $password);
        $users[$new_user->id] =& $new_user;
        if(!$new_user) return 3;
        return $users[$id];
    }

    function get_userid($username){
        global $db;
        return $db->select_var('user', 'id', [['username',$username]]);
    }

    function style_table($arr){
        $r = "<table class='striped'><thead><tr><th>Key</th><th>Value</th></tr></thead><tfoot><tr><th></th><th></th></tr></tfoot><tbody>";
        foreach($arr as $k => $v) $r .= "<tr><td>{$k}</td><td>{$v}</td></tr>";
        $r .= "</tbody></table>";
        return $r;
    }
    function style_dropdown($name, $placeholder, $values, $current = null){
        $r =  "<div class='picker metro' style='width:100% !important;'><select name='{$name}'><option>{$placeholder}</option>"; $og = false;
        foreach($values as $k => $v){ if($k == "_".$v){ if($og) $r .= "</optgroup>"; $r .= "<optgroup label='{$v}'>"; $og = true; } else $r .= "<option value='".( is_numeric($k) ? $v : $k )."' ".( $current == ( is_numeric($k) ? $v : $k ) ? "SELECTED" : "")."  >{$v} " . ( $user->debug && $v != $k ? "[ {$k} ]" : "" ) . " </option>"; }
        if($og) $r .= "</optgroup>"; $r .= "</select></div>";
        return $r;
    }
    function style_field($s){ return "<div class='field metro'>{$s}</div>"; }
    function style_text_input($name, $placeholder = null, $content = null, $size = '', $css = '', $type = 'text'){ return "<input class='{$size} input text' style=\"{$css}\" name='{$name}' type='{$type}' placeholder=\"{$placeholder}\" value=\"{$content}\" />"; }


    /** Include Login Listener Function */
    require_once('functions/login_listen.func.php');
