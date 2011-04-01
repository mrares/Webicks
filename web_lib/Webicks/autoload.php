<?php
//namespace_autoloader.php

function namespace_autoloader($class) {
	//Strip the first \
    if('\\' === $class[0]) {
        $class = substr($class, 1);
    }
    $filePath = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    require_once $filePath . '.php';
}



function getPath( $class ) {
    $file = null;
    if(false !== ($pos = strrpos($class, '\\'))) {
        // namespaced class name
        $namespace = substr($class, 0, $pos);
        foreach($this->namespaces as $ns=>$dir) {
            if(0 === strpos($namespace, $ns)) {
                $className = substr($class, $pos + 1);
                return $dir . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
            }
        }
    }
    else {
        // PEAR-like class name
        foreach($this->prefixes as $prefix=>$dir) {
            if(0 === strpos($class, $prefix)) {
                return $dir . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
            }
        }
    }

    return false;
}
















function prefix_autoloader( $className ) {
    $classPath = str_replace('_', '/', $className);
    require_once $classPath;
}