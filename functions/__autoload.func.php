<?php
/**
 * Class Autoloader
 *
 * Loads the Class file if it exists, as well as the class helper file if it exists
 *
 * @param string $n Name
 * @return void
 */
function __autoload($n){
    foreach(['','Database/','DOM/'] as $folder){
        $c = ROOT_DIR.'/class/' . $folder . $n . '.class.php';
        if(file_exists($c)){
            require_once( $c );
            break;
        }
    }
}
