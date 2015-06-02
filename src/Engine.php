<?php
namespace SNTools\Framework;
use SNTools\Framework\Fallback\FallbackApp;
use SNTools\Filter\FilterInput;

/**
 * Framework engine
 *
 * @author Darth Killer
 * @version 2.0-dev
 */
final class Engine {
    /*
     * Configuration document
     * @var \DOMDocument
     */
    private static $config;
    /**
     *
     * @var \DOMXPath
     */
    private static $xpath;
    
    private function __construct() {}
    
    /**
     * 
     * @param string $configpath
     * @throws EngineException
     */
    private static function load($configpath) {
        if(!file_exists($configpath))
            throw new EngineException('Config file not found : ' . $configpath);
        $xsd = __DIR__ . '/../config.xsd';
        if(!file_exists($xsd))
            throw new EngineException('Config schema not found : ' . $xsd);
        $config = new \DOMDocument;
        if(!$config->load($configpath))
            throw new EngineException('Failed to load config file : ' . $configpath);
        if(!$config->schemaValidate($xsd))
            throw new EngineException('Invalid config file : ' . $configpath);
        self::$config = $config;
        self::$xpath = new \DOMXPath(self::$config);
    }
    
    /**
     * 
     * @param Application $app
     * @param \DOMElement $routeNode
     * @param string $url
     * @return Route
     */
    private static function routeBuilder (Application $app, \DOMElement $routeNode, $url = '/') {
        $controller = self::$xpath->query('controller', $routeNode)->item(0);
        /* @var $controller \DOMElement */
        $varNames = array();
        foreach(self::$xpath->query('vars/var', $routeNode) as $varNode) {
            /* @var $varNode \DOMElement */
            $varNames[] = $varNode->textContent;
        }
        return new Route($app, 
                $url, 
                $controller->textContent, 
                $controller->getAttribute('action'), 
                $controller->hasAttribute('view') ? $controller->getAttribute('view') : null, 
                $varNames);
    }

    /**
     * Tries to find an application matching
     * @return Application
     * @throws ClassNotFoundException
     * @throws InvalidClassException
     * @throws NoRouteException
     */
    private static function getApp() {
        $filter = new FilterInput();
        foreach(self::$xpath->query(sprintf("//host[@domain='%s']", $filter->filter(FilterInput::SERVER, 'SERVER_NAME'))) as $host) {
            /* @var $host \DOMElement */
            $expectHTTPS = ($host->hasAttribute('https') and $host->getAttribute('https'));
            if(!($filter->filter(FilterInput::SERVER, 'REQUEST_SCHEME') == 'https' xor $expectHTTPS)) {
                foreach(self::$xpath->query('application', $host) as $appNode) {
                    /* @var $appNode \DOMElement */
                    $regex = sprintf('#^%s#', $appNode->hasAttribute('url-prefix') ? $appNode->getAttribute('url-prefix') : '/');
                    if(preg_match($regex, $filter->filter(FilterInput::SERVER, 'REQUEST_URI'))) {
                        if(!class_exists($classname = $appNode->getAttribute('class')))
                            throw new ClassNotFoundException($classname);
                        if(!is_subclass_of($classname, '\SNTools\Framework\Application'))
                            throw new InvalidClassException($classname);
                        $app = new $classname();
                        /* @var $app Application */
                        
                        $twigNode = self::$xpath->query('config/twig', $appNode)->item(0);
                        /* @var $twigNode \DOMElement */
                        foreach(self::$xpath->query('@*', $twigNode) as $optionAttribute) {
                            /* @var $optionAttribute \DOMAttr */
                            $app->config->addOption($optionAttribute->name, $optionAttribute->value);
                        }
                        foreach(self::$xpath->query('template_dir', $twigNode) as $dirNode) {
                            /* @var $dirNode \DOMElement */
                            $app->config->addDir($dirNode->textContent, $dirNode->hasAttribute('relative') ? $dirNode->getAttribute('relative') : null);
                        }
                        $cacheQuery = self::$xpath->query('cache_dir', $twigNode);
                        if($cacheQuery->length) {
                            $dirNode = $cacheQuery->item(0);
                            /* @var $dirNode \DOMElement */
                            $app->config->addDir($dirNode->textContent, $dirNode->hasAttribute('relative') ? $dirNode->getAttribute('relative') : null, true);
                        }
                        
                        foreach(self::$xpath->query('config/parameters/parameter', $appNode) as $paramNode) {
                            /* @var $paramNode \DOMElement */
                            $app->config[$paramNode->getAttribute('name')] = $paramNode->textContent;
                        }
                        
                        
                        foreach(self::$xpath->query('router/route', $appNode) as $routeNode) {
                            /* @var $routeNode \DOMElement */
                            $app->addRoute(self::routeBuilder($app, $routeNode, $routeNode->getAttribute('url')));
                        }
                        $error404Query = self::$xpath->query('router/error404', $appNode);
                        if($error404Query->length) {
                            $app->error_route = self::routeBuilder($app, $error404Query->item(0));
                        }
                        
                        return $app;
                    }
                }
                throw new NoRouteException('No application found');
            }
        }
        throw new NoRouteException('Host not found');
    }

    /**
     * Executes the engine
     * @param string $configpath Path of the XML configuration file
     */
    public static function run($configpath) {
        try {
            try {
                self::load($configpath);
                $app = self::getApp();
                $app->run();
            }
            catch(ConfigurationException $ex) {
                try {
                    $app = new FallbackApp();
                    $app->request->registerParameters(array('error' => $ex->getMessage(), 'errtype' => get_class($ex)));
                    $app->run();
                }
                catch(ConfigurationException $ex2) {
                    throw new EngineException('Built-in application configuration error : ' . $ex2->getMessage(), 0, $ex);
                }
            }
        }
        catch(EngineException $ex) {
            for($error = ''; $ex; $ex = $ex->getPrevious()) {
                $error .= sprintf('%s ; line %d of file %s', $ex->getMessage(), $ex->getLine(), $ex->getFile());
                $error .= "\n";
            }
            trigger_error($error, E_USER_ERROR);
        }
    }
}
