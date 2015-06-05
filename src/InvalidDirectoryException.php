<?php
namespace SNTools\Framework;

/**
 * Invalid Directory detected from configuration
 *
 * @author Samy Naamani
 * @version 2.0-dev
 */
class InvalidDirectoryException extends ConfigurationException {
    /**
     * 
     * @param string $directory Directory path. Message will be built from it
     * @param int $code
     * @param \Exception $previous Exception that caused this exception, if any
     */
    public function __construct($directory, $code = 0, \Exception $previous = null) {
        parent::__construct("Invalid directory : $directory", $code, $previous);
    }
}
