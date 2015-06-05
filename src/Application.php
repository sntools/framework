<?php
namespace SNTools\Framework;
use SNTools\Object;
use SNTools\PropertyException;

/**
 * Parent class for all applications to be handled with the framework engine
 *
 * @author Samy Naamani
 * @version 2.0-dev
 * @property-read string $name Application name
 * @property-read HTTPRequest $request HTTP Request object
 * @property-read HTTPResponse $response HTTP Response object
 * @property Config $config Application configuration
 * @property Route $error_route Application route to use for errors
 */
abstract class Application extends Object {
    /**
     * Application name
     * @var string
     * @see Application::$name
     */
    private $_name;
    /**
     * HTTP Request object
     * @var HTTPRequest
     * @see Application::$request
     */
    private $_request;
    
    /**
     * HTTP Response object
     * @var HTTPResponse
     * @see Application::$response
     */
    private $_response;
    
    /**
     * Application configuration
     * @var Config
     * @see Application::$config
     */
    private $_config = null;
    
    /**
     * List of routes used by the application
     * @var Route[]
     */
    private $_routes = array();
    
    /**
     * Application route to use for errors
     * @var Route
     * @see Application::$error_route
     */
    private $_error_route = null;
    
    /**
     * Application constructor
     */
    public function __construct() {
        parent::__construct();
        $this->_name = basename(get_called_class());
        $this->_request = new HTTPRequest($this);
        $this->_response = new HTTPResponse($this);
        $this->_config = new Config($this);
    }
    
    /**
     * Property getters handler
     * @param string $name Property name
     * @return mixed Property value
     * @throws PropertyException
     * @see Object::__get()
     */
    public function __get($name) {
        switch($name) {
            case 'name':
                return $this->_name;
            case 'request':
                return $this->_request;
            case 'response':
                return $this->_response;
            case 'config':
                return $this->_config;
            case 'error_route':
                return $this->_error_route;
            default:
                return parent::__get($name);
        }
    }
    
    /**
     * Property setters handler
     * @param string $name Property name
     * @param mixed $value Property value
     * @throws PropertyException
     * @see Object::__set()
     */
    public function __set($name, $value) {
        switch($name) {
            case 'config':
                if(!($value instanceof Config))
                    throw new PropertyException('Config object excepted, ' . gettype($value) . ' given.');
                elseif($value->app != $this)
                    throw new PropertyException('Foreign application given');
                $this->_config = $value;
                break;
            case 'error_route':
                if(!($value instanceof Route))
                    throw new PropertyException('Route object excepted, ' . gettype($value) . ' given.');
                elseif($value->app != $this)
                    throw new PropertyException('Foreign application given');
                $this->_error_route = $value;
                break;
            default:
                parent::__set($name, $value);
        }
    }

    /**
     * Conversion to string
     * @return string
     * @see Object::__toString()
     */
    public function __toString() {
        return $this->_name->getValue();
    }

    /**
     * Runs the application
     */
    abstract public function run();

    /**
     * Method to returns the working directory of the subclass implementing it
     * @todo Find a way to define this behaviour directly in superclass
     * @return string
     */
    abstract public function applicationDir();
    
    /**
     * Add a route to the route list
     * @param Route $route Route to add
     */
    public function addRoute(Route $route) {
        $this->_routes[] = $route;
    }
    
    /**
     * Retrive a controller from a route matching requested url.
     * If none, fallsback to error route and error controller.
     * If no error route, throws an exception to be caught by the engine.
     * @return BackController Found controller
     * @throws NoRouteException
     * @throws ClassNotFoundException
     * @throws InvalidClassException
     * @uses Application::getControllerFromRoute()
     */
    final protected function getController() {
        foreach($this->_routes as $route) {
            if($route->match()) return $this->getControllerFromRoute($route);
        }
        if(!is_null($this->error_route)) return $this->getControllerFromRoute ($this->error_route);
        throw new NoRouteException('No route found');
    }
    
    /**
     * Reads a route and retrives a controller from it
     * @param Route $route
     * @return BackController
     * @throws ClassNotFoundException
     * @throws InvalidClassException
     */
    final private function getControllerFromRoute(Route $route) {
        if(!class_exists($route->controller))
            throw new ClassNotFoundException('Unknown controller');
        elseif(!is_subclass_of($route->controller, '\SNTools\Framework\BackController'))
            throw new InvalidClassException('Invalid controller');
        return new $route->controller($this, $route);
    }
}
