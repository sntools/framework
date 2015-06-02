<?php
namespace SNTools\Framework;

/**
 * Superclass for all controllers
 *
 * @author Samy NAAMANI
 * @version 2.0-dev
 * @property-read string $module Module name
 * @property-read string $action Action to perform
 * @property-read string|null $view View to use. Null if N/A
 * @todo Find a usage for the module
 */
abstract class BackController extends Component {
    /**
     * Inner matched route.
     * @var Route
     * @see BackController::$action
     * @see BackController::$view
     */
    private $route;
    
    /**
     * Module name
     * @var string
     * @see BackController::$module
     */
    protected $_module;

    /**
     * Controller constructor
     * @param Application $app Parent application
     * @param Route $route Route matched by the controller
     */
    public function __construct(Application $app, Route $route) {
        parent::__construct($app);
        $this->route = $route;
    }

    /**
     * Property getters handler
     * @param string $name Property name
     * @return mixed Property value
     * @throws \SNTools\PropertyException
     * @see Component::__get()
     */
    public function __get($name) {
        switch ($name) {
            case 'module':
                return $this->_module;
            case 'action':
            case 'view':
                return $this->route->$name;
            default:
                return parent::__get($name);
        }
    }
    
    /**
     * Executes the controller. For 'foo' action, will look for method 'executeFoo()' defined in subclass.
     * @throws UndefinedActionException
     */
    final public function execute() {
        $method = 'execute' . ucfirst($this->action);
        if(!is_callable(array($this, $method)))
            throw new UndefinedActionException($this->action);
        $this->$method();
    }
}
