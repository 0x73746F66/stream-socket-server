<?php
define('PROJECT_ROOT', realpath(str_replace('/examples', '/src', dirname(__FILE__))) . DIRECTORY_SEPARATOR );
$current = spl_autoload_extensions();
spl_autoload_extensions($current);
spl_autoload_register(function($file){
  $ext = '.php';
  $pieces = explode('\\',$file);
  $fileName = end($pieces);
  if ($fileName[0] === 'i') {
    $fileName = ltrim($fileName, 'i').'Interface';
  }
  include_once PROJECT_ROOT.$fileName.$ext;
});
