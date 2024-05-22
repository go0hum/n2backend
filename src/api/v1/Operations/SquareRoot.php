<?php 
class SquareRoot implements InterfaceOperation {
    public function execute(array $input) {
        return sqrt($input['a']);
    }
}
