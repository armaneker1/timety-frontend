<?php

$error_handling = true;
require_once __DIR__ . '/../utils/Functions.php';

class TimetyRegisterTester extends WebTestCase {

    function testRegisterLogin() {
        $this->get(PAGE_LOGIN);
        $this->setField('te_username', 'keklikhasan');
        $this->setField('te_password', 'e0pvdcn1');
        $this->assertSubmit('Login');
        $this->click('Login');
        $this->assertTitle("Timety | Never miss out");
    }

    function testRegisterLoginFail() {
        $this->get(PAGE_LOGIN);
        $this->setField('te_username', 'keklikhasan');
        $this->setField('te_password', 'failfail');
        $this->assertSubmit('Login');
        $this->click('Login');
        $this->assertTitle("Timety | Login");
    }

}

?>
