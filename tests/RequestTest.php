<?php
namespace yapaf;
class RequestTest extends \PHPUnit_Framework_TestCase
{
    public function setUp() {
        $this->request = new Request;
    }

    public function testRequestParamsCanBeReadAndWritten() {
        $this->request->set('somekey', 'somevalue');
        $this->assertEquals('somevalue', $this->request['somekey']);
    }
}



