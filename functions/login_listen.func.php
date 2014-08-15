<?php
/**
 * Login Listen
 *
 * Listen for login/logout requests and handle them
 *
 * @return void
 */
function login_listen(){
    global $auth;
    $redir = "<script>window.location = '?loggedin=%s';</script>";
    if(!empty($_POST['u']) && !empty($_POST['p'])){ printf($redir, ( $auth->login($_POST['u'], $_POST['p']) ? 'y' : 'n' )); exit(); }
    if(!empty($_GET['logout'])){ $auth->logout(); printf($redir,'y'); exit(); }
}