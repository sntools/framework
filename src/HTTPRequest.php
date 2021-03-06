<?php
namespace SNTools\Framework;
use SNTools\Server;

/**
 * HTTP Request descriptor.
 * Handles various types of parameters (GET, POST, COOKIE, URL-detected)
 * Also provides some request metadata
 *
 * @author Samy Naamani
 * @version 2.0-dev
 * @property-read string $method Request method used
 * @property-read string $uri Request URI
 */
class HTTPRequest extends Component {
    
    /**
     * List of URL-detected parameters
     * @var array
     */
    private $_params = array();
    /**
     *
     * @var Server 
     */
    private $server;

    public function __construct(Application $app) {
        parent::__construct($app);
        $this->server = new Server();
    }
    /**
     * Property getters handler
     * @param string $name Property name
     * @return mixed Property value
     * @throws \DomainException
     * @see Component::__get()
     */
    public function __get($name) {
        switch($name) {
            case 'method':
                return $this->server['REQUEST_METHOD'];
            case 'uri':
                return $this->server['REQUEST_URI'];
            default:
                return parent::__get($name);
        }
    }
    
    /**
     * Factorisation method : parameter getter for GET, POST and COOKIE parameters
     * @param string $name Parameter name
     * @param int $input INPUT_* constant for filtering
     * @return mixed Parameter value, null if none
     */
    private function getElement($name, $input) {
        return filter_input($input, $name);
    }
    /**
     * Factorisation method : parameter existance checker for GET, POST and COOKIE parameters
     * @param string $name Parameter name
     * @param int $input INPUT_* constant for filtering
     * @return boolean Parameter exists, or not
     */
    private function hasElement($name, $input) {
        return !is_null($this->getElement($name, $input));
    }
    /**
     * Cookie getter
     * @param string $name Cookie name
     * @return mixed Cookie value, null if none
     */
    public function cookie($name) {
        return $this->getElement($name, INPUT_COOKIE);
    }
    /**
     * Cookie existance checker
     * @param string $name Cookie name
     * @return boolean Cookie exists, or not
     */
    public function cookieExists($name) {
        return $this->hasElement($name, INPUT_COOKIE);
    }
    /**
     * GET parameter getter
     * @param string $name Parameter name
     * @return mixed Parameter value, null if none
     */
    public function get($name) {
        return $this->getElement($name, INPUT_GET);
    }
    /**
     * GET parameter existance checker
     * @param string $name Parameter name
     * @return boolean Parameter exists, or not
     */
    public function getExists($name) {
        return $this->hasElement($name, INPUT_GET);
    }
    /**
     * POST parameter getter
     * @param string $name Parameter name
     * @return mixed Parameter value, null if none
     */
    public function post($name) {
        return $this->getElement($name, INPUT_POST);
    }
    /**
     * POST parameter existance checker
     * @param string $name Parameter name
     * @return boolean Parameter exists, or not
     */
    public function postExists($name) {
        return $this->hasElement($name, INPUT_POST);
    }
    /**
     * URL-detected parameter getter
     * @param string $name Parameter name
     * @return mixed Parameter value, null if none
     */
    public function getParameter($name) {
        return $this->hasParameter($name) ? $this->_params[$name] : null;
    }
    /**
     * URL-detected parameter existance checker
     * @param string $name Parameter name
     * @return boolean Parameter exists, or not
     */
    public function hasParameter($name) {
        return isset($this->_params[$name]);
    }
    /**
     * Register URL-detected parameter list
     * @param array $parameters
     */
    public function registerParameters(array $parameters) {
        $this->_params = array_merge($this->_params, $parameters);
    }
}
