<?php
namespace SNTools\Framework;

/**
 * Invalid template class detected in configuration
 *
 * @author Darth Killer
 * @version 2.0-dev
 */
class InvalidClassException extends ConfigurationException {
    /**
     * 
     * @param string $classname Class that was being used as template class. Will be used to build message.
     * @param int $code
     * @param \Exception $previous Exception that caused this exception, if any.
     */
    public function __construct($classname, $code = 0, \Exception $previous = null) {
        parent::__construct("Invalid class used : $classname", $code, $previous);
    }
}
