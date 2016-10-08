<?php
$util = include_once __DIR__.'/lib/Util.php';
function __alga_autoload__ ($class)
{
    $root = __DIR__;
    
    // permette di poter caricare classi che non sono in "src" ma sono in "features"
    $features = 'features\\';
    if (substr($class,0,strlen($features)) == $features) {
        $root = dirname($root);
    }
    
    $path_class = $root. DIRECTORY_SEPARATOR. str_replace('\\', DIRECTORY_SEPARATOR, $class).'.php';
    /*
    if (!file_exists($path_class)) {
        $path_parts = pathinfo($path_class);

        if (strpos($path_parts['basename'], 'Interface') !== false) {
            $path_class = implode(DIRECTORY_SEPARATOR, array($path_parts['dirname'], "interfaces", $path_parts['basename']));
        }
    }
*/
    if (file_exists($path_class)) {
        require_once $path_class;
    } else {
        var_dump(getcwd(), $path_class, $class);
        $fname =  explode(DIRECTORY_SEPARATOR, str_replace('\\', DIRECTORY_SEPARATOR, $class));
        $fname[count($fname)-1] = 'class.'.$fname[count($fname)-1].'.php';
        $path = __DIR__.DIRECTORY_SEPARATOR.'classes'.strtolower(DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR,$fname)); 

        if (file_exists($path)) {
            require_once $path;
        }
    }
}
spl_autoload_register(array($util, 'autoload'));
