<?php
namespace SNTools\Framework;

/**
 * Attempted to call an undefined action
 *
 * @author Samy NAAMANI
 * @version 2.0-dev
 */
class UndefinedActionException extends ConfigurationException {
    /**
     * Exception constructor
     * @param string $action Action that was called. Will be used to generate message.
     * @param int $code
     * @param \Exception $previous Exception that caused this exception, if any
     */
    public function __construct($action, $code = 0, \Exception $previous = null) {
        parent::__construct("Undefined action $action", $code, $previous);
    }
}
