<?php
namespace Mach;

/*
 * Borrowed from Symfony2 UniversalAutoloader
 * Autoloader implements a "universal" autoloader for PHP 5.3.
 *
 * Example usage:
 *
 *     $loader = new Autoloader();
 *
 *     // register classes with namespaces
 *     $loader->registerNamespaces(array(
 *       'Symfony\Component' => __DIR__.'/component',
 *       'Symfony' => __DIR__.'/framework',
 *     ));
 *
 *     // register a library using the PEAR naming convention
 *     $loader->registerPrefixes(array(
 *       'Swift_' => __DIR__.'/Swift',
 *     ));
 *
 *     // activate the autoloader
 *     $loader->register();
 *
 *
 */

class Autoloader {

    protected $namespaces = array();
    protected $prefixes = array();
    protected $map = array();

    public function getMap() {
        return $this->map;
    }

    public function getNamespaces() {
        return $this->namespaces;
    }

    public function getPrefixes() {
        return $this->prefixes;
    }

    /**
     * Registers an array of namespaces
     *
     * @param array $namespaces An array of namespaces (namespaces as keys and locations as values)
     */
    public function registerNamespaces(array $namespaces) {
        $this->namespaces = array_merge($this->namespaces, $namespaces);
    }

    /**
     * Registers a namespace.
     *
     * @param string $namespace The namespace
     * @param string $path      The location of the namespace
     */
    public function registerNamespace($namespace, $path) {
        $this->namespaces[$namespace] = $path;
    }

    /**
     * Registers an array of classes using the PEAR naming convention.
     *
     * @param array $classes An array of classes (prefixes as keys and locations as values)
     */
    public function registerPrefixes(array $classes) {
        $this->prefixes = array_merge($this->prefixes, $classes);
    }

    /**
     * Registers a set of classes using the PEAR naming convention.
     *
     * @param string $prefix The classes prefix
     * @param string $path   The location of the classes
     */
    public function registerPrefix($prefix, $path) {
        $this->prefixes[$prefix] = $path;
    }

    /**
     * Registers this instance as an autoloader.
     */
    public function register() {
        spl_autoload_register(array($this, 'loadClass'));
    }

    public function getPath($class) {
        $file = null;
        if(false !== ($pos = strrpos($class, '\\'))) {
            // namespaced class name
            $namespace = substr($class, 0, $pos);
            foreach($this->namespaces as $ns => $dir) {
                if(0 === strpos($namespace, $ns)) {
                    $className = substr($class, $pos + 1);
                    return $dir . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
                }
            }
        } else {
            // PEAR-like class name
            foreach($this->prefixes as $prefix => $dir) {
                if(0 === strpos($class, $prefix)) {
                    return $dir . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
                }
            }
        }

        return false;
    }

    /**
     * Loads the given class or interface.
     *
     * @param string $class The name of the class
     */
    public function loadClass($class) {
        if('\\' === $class[0]) {
            $class = substr($class, 1);
        }

        $file = $this->getPath($class);
        if($file === false) {
            return false;
        }

        $this->map[$class] = $file;
        $this->_checkFileIfIsDevel($file);
        require $file;
        return true;
    }

    private function _checkFileIfIsDevel($file) {
        //hardcoded because otherwise we must require it in production
//        if(APPLICATION_ENV == 'production') {
//            return;
//        }
        if(!is_file($file)) {
            throw new \Exception("File not found in autoloader: $file");
        }
    }

}