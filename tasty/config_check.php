<?php
## config_check.php -- list out all directives in php.ini

$path = '/users/mespinosa/.php_files/';
set_include_path(get_include_path().PATH_SEPARATOR.$path);

phpinfo();

?>