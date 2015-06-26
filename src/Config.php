<?php
namespace SNTools\Framework;
use SNTools\Server;

/**
 * Application configuration descriptor.
 * Use as an array to access application-specific parameters.
 *
 * @author Samy Naamani
 * @version 2.0-dev
 * @property-read array $twig_dirs
 * @property-read array $twig_options
 */
final class Config extends Component implements \IteratorAggregate, \ArrayAccess {
    const PATHDIR_APPDIR = 'appdir';
    const PATHDIR_DOCROOT = 'docroot';
    const PATHDIR_NONE = 'none';
    /**
     * Configuration-specific parameter list
     * @var array
     */
    private $parameters = array();
    /**
     * Twig template directories
     * @var array
     * @see Config::$twig_dirs
     */
    private $_twig_dirs = array();
    
    /**
     * Twig option list
     * @var array
     * @see Config::$twig_options
     */
    private $_twig_options = array();

    /**
     * Property getters handler
     * @param string $name Property name
     * @return mixed Property value
     * @throws \SNTools\PropertyException
     * @see Component::__get()
     */
    public function __get($name) {
        switch($name) {
            case 'twig_dirs':
            case 'twig_options':
                $attr = "_$name";
                return $this->$attr;
            default:
                return parent::__get($name);
        }
    }

    /**
     * Adds a Twig template directory, or sets up the Twig cache directory
     * @param string $dir Directory to setup
     * @param string $relative Relative path policy. Use class constant as values in the configuration file.
     * @param boolean $isCacheDir If true, directory is the Twig cache directory. Otherwise, adds a template directory.
     * @throws InvalidDirectoryException
     */
    public function addDir($dir, $relative, $isCacheDir = false) {
        switch($relative) {
            case self::PATHDIR_APPDIR:
                $prefix = $this->app->applicationDir();
                break;
            case self::PATHDIR_DOCROOT:
                $server = new Server();
                $prefix = $server['DOCUMENT_ROOT'];
                break;
            case self::PATHDIR_NONE:
            default:
                $prefix = '';
                break;
        }
        if('/' != substr($dir, 0, 1))
            $dir = sprintf('%s/%s', $prefix, $dir);
        if(file_exists($dir)) {
            if($isCacheDir) $this->_twig_options['cache'] = $dir;
            else $this->_twig_dirs[] = $dir;
        } else throw new InvalidDirectoryException($dir);
    }

    /**
     * 
     * @param string $option Option name. Invalid names will be ignored
     * @param mixed $value Option value
     * @throws ClassNotFoundException
     * @throws InvalidClassException
     */
    public function addOption($option, $value) {
        switch($option) {
            case 'debug':
            case 'auto-reload':
            case 'auto_reload':
                $this->_twig_options[str_replace('-', '_', $option)] = (bool)$value;
                break;
            case 'base-template-class':
            case 'base_teplate_class':
                if(!class_exists($value))
                    throw new ClassNotFoundException($value);
                if(is_subclass_of($value, '\Twig_Template'))
                    $this->_twig_options['base_template_class'] = $value;
                else throw new InvalidClassException($value);
                break;
            case 'charset':
            case 'autoescape':
                $this->_twig_options[$option] = $value;
                break;
        }
    }
    public function getIterator() {
        return new \ArrayIterator($this->parameters);
    }
    
    public function offsetExists($offset) {
        return isset($this->parameters[$offset]);
    }
    
    public function offsetGet($offset) {
        return $this->offsetExists($offset) ? $this->parameters[$offset] : null;
    }
    
    public function offsetSet($offset, $value) {
        $this->parameters[$offset] = $value;
    }
    
    public function offsetUnset($offset) {
        unset($this->parameters[$offset]);
    }
}
