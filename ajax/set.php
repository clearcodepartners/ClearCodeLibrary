<?php
// ajax/set/type/Name/id/1/var/last/val/Flynn/
    session_start();
    require_once('../init.php');
    if(!$auth->logged_in()){ echo json_encode([ 'success' => false, 'msg' => 'Not Logged In', 'result' => [] ]); exit();}
    $class      = $_GET['type'];
    $id         = $_GET['id'];
    $var        = $_GET['var'];
    $val        = $_POST['val'];
    $cb         = $_POST['callback'];
    $c          = new $class($id);
    $c->$var    = $val;
    echo json_encode([ 'success' => true, 'callback' => $cb, 'result' => [ 'var' => $var , 'val' => $c->$var ] ]); exit();