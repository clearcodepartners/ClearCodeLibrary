<?php
/**
 * Dump the input to the screen and die
 * @param mixed
 * @return void
 */
function dump($o, $die = true){ echo "<pre>"; var_dump($o); echo "</pre>"; if($die) die(); }