<?php
/**
 * Load the Database if it's not loaded already
 * @global Database the global Database handler {@link $db}
 * @return Database
 */
function &load_db(){
    global $db;
    if(empty($db)) $db = new Database;
    if($db->connected === false){
        //$db->connect("localhost", "pillhelp", "E%1-lT", "phworks");
        $db->connect("localhost", "root", "m00c0wz88", "clearcodelibrary");
    }
    return $db;
}