<?php
namespace SNTools\Framework;

/**
 * HTTP Response descriptor
 * Also in charge of headers, redirections, setting cookies.
 * Used as an array, sets up parameters avilable within Twig views
 * Can either send to client a Twig view, or a JSON-encoded response
 *
 * @author Darth Killer
 * @version 2.0-dev
 * @todo Add HTTP Response codes as class constants
 */
class HTTPResponse extends Component implements \ArrayAccess {
    
    /**
     * Parameter list accessible in Twig views
     * @var array
     */
    private $parameters = array();
    
    /**
     * Set cookie
     * @param string $name Cookie name
     * @param mixed $value Cookie value, defaults to empty string
     * @param int $expire Expire time, defaults to 0 seconds
     * @param string|null $path Cookie path, defaults to null to let PHP handle it
     * @param string|null $domain Cookie domain, defaults to null to let PHP handle it
     * @param boolean $secure Use secured cookie. Defaults to false.
     * @param boolean $httponly Use HTTP-only cookie. Defaults to true.
     */
    public function setCookie($name, $value='', $expire=0, $path=null, $domain=null, $secure=false, $httponly=true) {
        setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
    }
    
    /**
     * Add a HTTP header
     * @param string $header Header to add
     */
    public function addHeader($header) {
        header($header);
    }
    
    /**
     * Set up response code
     * @param int $code Response code. Use class constants for easy access.
     */
    public function setResponseCode($code) {
        http_response_code($code);
    }
    
    /**
     * Trigger a location redirection header
     * @param string $location New location
     */
    public function redirect($location) {
        header("Location: $location");
        exit;
    }
    
    /**
     * Sends contents via Twig view
     * @param string $view Twig view to use
     */
    public function send($view) {
        $loader = new \Twig_Loader_Filesystem($this->app->config->twig_dirs);
        $twig = new \Twig_Environment($loader, $this->app->config->twig_options);
        echo $twig->render($view, $this->parameters);
    }
    
    /**
     * Sends contents as JSON-encoded object
     * @param array|object|\JsonSerializable $element Object to encode and send
     */
    public function sendJson($element) {
        $this->addHeader('Content-type: text/json');
        echo json_encode($element);
    }

    /**
     * Parameter existance checker. Checks existance of a parameter in the list intended for Twig views.
     * @param string $offset Parameter name
     * @return boolean Parameter exists, or not.
     * @see \ArrayAccess
     */
    public function offsetExists($offset) {
        return isset($this->parameters[$offset]);
    }
    
    /**
     * Parameter getter. Gets value of a parameter in the list intended for Twig views.
     * @param string $offset Parameter name
     * @return mixed Parameter value, null if none.
     * @see \ArrayAccess
     */
    public function offsetGet($offset) {
        return $this->offsetExists($offset) ? $this->parameters[$offset] : null;
    }
    
    /**
     * Parameter setter. Sets value of a parameter in the list intended for Twig views.
     * @param string $offset Parameter name
     * @param mixed $value Parameter value
     * @see \ArrayAccess
     */
    public function offsetSet($offset, $value) {
        $this->parameters[$offset] = $value;
    }
    
    /**
     * Parameter unsetter. Unsets value of a parameter in the list intended for Twig views.
     * @param string $offset Parameter name
     * @see \ArrayAccess
     */
    public function offsetUnset($offset) {
        unset($this->parameters[$offset]);
    }
}
