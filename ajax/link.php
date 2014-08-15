<?php
session_start();
require_once('../init.php');
if(!$auth->logged_in()){ echo json_encode([ 'success' => false, 'msg' => 'Not Logged In', 'result' => [] ]); exit();}
$class      = $_REQUEST['type'];
$id         = $_REQUEST['id'];
$var        = $_REQUEST['var'];
$val_type   = $_REQUEST['val_type'];
$val_id     = $_REQUEST['val_id'];
$c          = new $class($id);
$c->$var    = new $val_type($val_id);
echo json_encode([ 'success' => true, 'result' => $c->$var ]); exit();