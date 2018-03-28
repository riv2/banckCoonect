<?php
require_once '../cback/boot.php';


class HahnAirTests extends PHPUnit_Framework_TestCase
{
    private $HahnAir;
    private $answer = array('status' => 'ok');


    protected function setUp()
    {
        $this->HahnAir = new HahnAir();
    }

    protected function tearDown()
    {
        $this->HahnAir = NULL;
    }

    public function testGoQuery()
    {
        $result = $this->HahnAir->GoQuery();
        $this->assertEquals($this->answer, $result);
    }

}
