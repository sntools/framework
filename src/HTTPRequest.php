<?php
namespace SNTools\Framework;
use SNTools\Filter\FilterInput;

/**
 * HTTP Request descriptor.
 * Handles various types of parameters (GET, POST, COOKIE, URL-detected)
 * Also provides some request metadata
 *
 * @author Samy NAAMANI
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
     * Property getters handler
     * @param string $name Property name
     * @return mixed Property value
     * @throws \DomainException
     * @see Component::__get()
     */
    public function __get($name) {
        $filter = new FilterInput();
        switch($name) {
            case 'method':
                return $filter->filter(FilterInput::SERVER, 'REQUEST_METHOD');
            case 'uri':
                return $filter->filter(FilterInput::SERVER, 'REQUEST_URI');
            default:
                return parent::__get($name);
        }
    }
    
    /**
     * Factorisation method : parameter getter for GET, POST and COOKIE parameters
     * @param string $name Parameter name
     * @param int $input FilterInput class constant
     * @return mixed Parameter value, null if none
     */
    private function getElement($name, $input) {
        $filter = new FilterInput();
        return $filter->filter($input, $name);
    }
    /**
     * Factorisation method : parameter existance checker for GET, POST and COOKIE parameters
     * @param string $name Parameter name
     * @param int $input Filter input constant
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
        return $this->getElement($name, FilterInput::COOKIE);
    }
    /**
     * Cookie existance checker
     * @param string $name Cookie name
     * @return boolean Cookie exists, or not
     */
    public function cookieExists($name) {
        return $this->hasElement($name, FilterInput::COOKIE);
    }
    /**
     * GET parameter getter
     * @param string $name Parameter name
     * @return mixed Parameter value, null if none
     */
    public function get($name) {
        return $this->getElement($name, FilterInput::GET);
    }
    /**
     * GET parameter existance checker
     * @param string $name Parameter name
     * @return boolean Parameter exists, or not
     */
    public function getExists($name) {
        return $this->hasElement($name, FilterInput::GET);
    }
    /**
     * POST parameter getter
     * @param string $name Parameter name
     * @return mixed Parameter value, null if none
     */
    public function post($name) {
        return $this->getElement($name, FilterInput::POST);
    }
    /**
     * POST parameter existance checker
     * @param string $name Parameter name
     * @return boolean Parameter exists, or not
     */
    public function postExists($name) {
        return $this->hasElement($name, FilterInput::POST);
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
