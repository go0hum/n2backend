<?php
require_once dirname(__DIR__).'/../core/bootstrap.php';
require_once dirname(__DIR__).'/v1/Operations/InterfaceOperation.php';
require_once dirname(__DIR__).'/v1/Operations/Addition.php';
require_once dirname(__DIR__).'/v1/Operations/Subtraction.php';
require_once dirname(__DIR__).'/v1/Operations/Multiplication.php';
require_once dirname(__DIR__).'/v1/Operations/Division.php';
require_once dirname(__DIR__).'/v1/Operations/SquareRoot.php';
require_once dirname(__DIR__).'/v1/Operations/RandomString.php';

class OperationHandler 
{
    private $operations = [];

    public function __construct() {
        $this->operations['addition'] = new Addition();
        $this->operations['subtraction'] = new Subtraction();
        $this->operations['multiplication'] = new Multiplication();
        $this->operations['division'] = new Division();
        $this->operations['square_root'] = new SquareRoot();
        $this->operations['random_string'] = new RandomString();
    }

    public function handle($action, array $input) {
        if (isset($this->operations[$action])) {
            return $this->operations[$action]->execute($input);
        } else {
            return false; 
        }
    }
}