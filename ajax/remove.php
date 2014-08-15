<?php
// ajax/remove/catid/1/current/home/new/work
session_start();
require_once('../init.php');
if(!$auth->logged_in()){ echo json_encode([ 'success' => false, 'msg' => 'Not Logged In', 'result' => [] ]); exit();}
$catid      = $_GET['catid'];
$current    = $_GET['current'];
$c          = new Category($catid);
$ret        = $c->remove($current);
echo json_encode([ 'success' => true, 'result' => [  ] ]); exit();