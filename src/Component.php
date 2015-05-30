<?php
namespace SNTools\Framework;
use SNTools\Object;

/**
 * Common behavior of application components
 *
 * @author Darth Killer
 * @version 2.0-dev
 * @property-read Application $app Parent application
 */
abstract class Component extends Object {
    /**
     * Parent application
     * @var Application
     * @see Component::$app
     */
    private $_app;
    
    /**
     * Component constructor
     * @param Application $app Parent application
     * @see Object
     */
    public function __construct(Application $app) {
        parent::__construct();
        $this->_app = $app;
    }
    
    /**
     * Property getter handler
     * @param string $name Property name
     * @return mixed Property value
     * @throws \DomainException
     * @see Object::__get()
     */
    public function __get($name) {
        switch($name) {
            case 'app':
                return $this->_app;
            default:
                return parent::__get($name);
        }
    }
}
