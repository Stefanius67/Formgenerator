<?php
function autoRegister(string $strClassName) : void 
{
    $strInclude = '';
    if (strpos($strClassName, '\\') > 1) {
        // replace the namespace prefix with the base directory, replace namespace
        // separators with directory separators in the relative class name, append
        // with .php
        $strInclude = str_replace('\\', DIRECTORY_SEPARATOR, $strClassName) . '.php';
    }

    // if the file exists, require it
    if (strlen($strInclude) > 0) {
        $strInclude = dirname(__FILE__) . '/' . $strInclude;
        if (file_exists($strInclude)) {
            require $strInclude;
        }
    }
}

spl_autoload_register('autoRegister');
        
