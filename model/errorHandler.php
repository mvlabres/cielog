<?php

class ErrorHandler{

    public $result;
    public $hasError;
    public $errorMessage;
    
    public function __construct($result, $hasError, $errorMessage) {
        $this->result = $result;
        $this->hasError = $hasError;
        $this->errorMessage = $errorMessage;
    }
}

?>