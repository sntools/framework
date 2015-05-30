<?php
namespace SNTools\Framework;

/**
 * Class could not be found
 *
 * @author Darth Killer
 * @version 2.0-dev
 */
class ClassNotFoundException extends ConfigurationException {
    /**
     * 
     * @param string $classname Class that could not be found. Will be used to build message.
     * @param int $code
     * @param \Exception $previous Exception that caused this exception, if any.
     */
    public function __construct($classname, $code = 0, \Exception $previous = null) {
        parent::__construct("Class not found : $classname", $code, $previous);
    }
}
