<?php
require_once '../cback/sbol.php';


class SBOLTests extends PHPUnit_Framework_TestCase
{
    private $SBOL;
    private $answer = array('status' => 'ok');
    private $args;

    protected function setUp()
    {
        $this->SBOL = new SBOL();
        $this->args = new WtFuncArgs(array('orderId' => '319'));
    }

    protected function tearDown()
    {
        $this->SBOL = NULL;
    }

    public function testconfirmRzd()
    {
        $result = $this->SBOL->confirmRzd($this->args);
        $this->assertEquals($this->answer, $result);
    }

}
