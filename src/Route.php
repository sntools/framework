<?php
namespace SNTools\Framework;

/**
 * Route descriptor. Also checks if requested url matches expected url.
 *
 * @author Darth Killer
 * @version 2.0-dev
 * @property-read string $controller Controller class name (with namespace) to use with this route
 * @property-read string $action Controller action to call with this route
 * @property-read string|null $view View associated with this route and controller action. Null if N/A.
 */
final class Route extends Component {
    /**
     * URL to recognize when trying to match with requested URL.
     * @var string
     */
    private $url;
    /**
     * Controller class name (with namespace) to use with this route
     * @var string
     * @see Route::$controller
     */
    private $_controller;
    /**
     * Controller action to call with this route
     * @var string
     * @see Route::$action
     */
    private $_action;
    /**
     * View associated with this route and controller action. Null if N/A.
     * @var string|null
     * @see Route::$view
     */
    private $_view;
    /**
     * Name list of variables to recognize within requested url.
     * Variables are detected upon matching the route, and added to the request object as parameters.
     * @var string[]
     */
    private $varnames = array();
    
    /**
     * Route constructor
     * @param Application $app Parent application
     * @param string $url URL to recognize when matching with requested URL.
     * @param string $controller Controller class name (with namespace) to use with this route
     * @param string $action Controller action to call with this route
     * @param string|null $view View associated with this route and controller action. Null if N/A.
     * @param array $varnames Name list of variables to recognize within requested url.
     * Variables are detected upon matching the route, and added to the request object as parameters.
     * @see Component
     */
    public function __construct(Application $app, $url, $controller, $action, $view = null, array $varnames = array()) {
        parent::__construct($app);
        $this->url = $url;
        $this->_controller = $controller;
        $this->_action = $action;
        $this->_view = $view;
        $this->varnames = $varnames;
    }
    
    /**
     * Property getters handler
     * @param string $name Property name
     * @return mixed Property value
     * @throws \DomainException
     * @see Controller::__get()
     */
    public function __get($name) {
        switch($name) {
            case 'controller':
            case 'action':
            case 'view':
                $attr = "_$name";
                return $this->$attr;
            default:
                return parent::__get($name);
        }
    }

    /**
     * Matches requested URL with the URL stored within the route.
     * If variables are expected, will also combine stored variable names with detected values and
     * add them to the request object, as parameters.
     * @return boolean Requested URL matches the route's expected URL.
     */
    public function match() {
        $regex = sprintf('#^%s$#U', $this->url);
        if(preg_match($regex, $this->app->request->uri, $matches)) {
            if(count($this->varnames)) {
                array_shift($matches);
                $this->app->request->registerParameters(array_combine($this->varnames, $matches));
            }
            return true;
        } else return false;
    }
}
