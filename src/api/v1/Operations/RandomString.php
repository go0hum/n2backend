<?php 
class RandomString implements InterfaceOperation {
    public function execute(array $input) {
        $rand_org = new RandDotOrg('1201hs@gmail.com - zooxial.com');
        return $rand_org->get_strings(1, 8)[0];
    }
}
