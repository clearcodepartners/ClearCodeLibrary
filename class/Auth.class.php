<?php
class Auth extends Lib{
    function __construct(){
        $this->load_defaults();
        $this->load_user();
    }
    public function auth($username, $pass){
        $ip_attempts = $this->db->select_assoc('auth_log',['*'],[["ip", $this->get_ip()],['ts', date( 'Y-m-d H:i:s', time() + ( 2 * 60 * 60 ) ), '>']]);
        $id = get_userid($username);
        if(count($ip_attempts) > 6) return $this->log_auth(false, 'Max Attempts', $id ? $id : 0 );
        if(!$id) return $this->log_auth(false, 'Incorrect Username');
        $u = new User($id);
        if(!$u->password){
            global $metadata_info;
            var_dump($metadata_info);
            $u->dump();
        }
        if($u->password != md5($pass)) return $this->log_auth(false, 'Incorrect Password '.md5($pass).' does not match '.$u->password, $id);
        if(!$u->can('login')) return $this->log_auth(false, 'No Perms', $id);
        if($u->is_admin()) $_SESSION['admin'] = 1;
        $this->id = $id;
        $_SESSION['login'] = $id;
        $_SESSION['group'] = $id;
        return $this->log_auth(true, 'Match', $id);
    }

    public  function login      ($username, $pass)              { $this->log_login($username, $pass); return $this->auth($username, $pass); }
    public  function logout     ()                              { session_destroy(); }
    public  function logged_in  ()                              { return !empty($_SESSION['login']); }

    private function log_auth   ($pass, $message, $user = null) { $this->db->insert('auth_log', array('msg' => $message, 'status' => $pass ? 1 : 0, 'user_id' => is_numeric($user) ? $user : 0, 'ip' => $this->get_ip() )); return $pass; }
    private function log_login  ($u, $p)                        { $this->db->i('login_log',['username' => $u, 'password' => md5($p), 'ip' => $this->get_ip() ]); }
}