<?php
// ajax/add/id/1/type/Phone/title/work
session_start();
require_once('../init.php');
if(!$auth->logged_in()){ echo json_encode([ 'success' => false, 'msg' => 'Not Logged In', 'result' => [] ]); exit();}
$id         = $_GET['id'];
$type       = $_GET['type'];
$title      = $_GET['title'];
$c          = new Category($id);
$ret        = $c->add($title);

echo json_encode([ 'success' => true, 'result' => [ 'val' => $c->$ret->id, 'title' => $ret ] ]); exit();