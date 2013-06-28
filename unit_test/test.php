<?php
$error_handling=true;
require_once(__DIR__ . '/simpletest/autorun.php');
require_once(__DIR__ . '/simpletest/web_tester.php');

SimpleTest::prefer(new TextReporter());

class WebTests extends TestSuite {

    function WebTests() {
        $this->TestSuite('TimetyRegisterTester');
        $this->addFile('register_test.php');
    }

}

?>
