<?php 
class Addition implements InterfaceOperation 
{
    public function execute(array $input) 
    {
        return $input['a'] + $input['b'];
    }
}
