<?php
namespace SNTools\Framework\Fallback;
use SNTools\Framework\Application;

/**
 * Fallback application, used by the framework engine when an error is beyond normal applications' reach
 *
 * @author Samy NAAMANI
 * @version 2.0-dev
 */
class FallbackApp extends Application {
    /**
     * Method to returns the working directory of the application subclass
     * @return string
     */
    public function applicationDir() {
        return __DIR__;
    }
    
    /**
     *
     * @var Controller404
     */
    private $controller;
    
    public function __construct() {
        parent::__construct();
        $this->config->addDir('.', 'appdir');
        $this->config->addDir('../cache', 'appdir', true);
        $this->controller = new Controller404($this, new \SNTools\Framework\Route($this, '.*', __NAMESPACE__ . '\\Controller404', '404', '404.twig'));
    }
    
    /**
     * Runs the application
     */
    public function run() {
        $this->controller->execute();
    }
}
