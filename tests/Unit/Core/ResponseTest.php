<?php

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Core;

use OxidEsales\Eshop\Core\Header;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Utils;
use OxidSolutionCatalysts\Adyen\Core\Response;
use oxregistry;
use PHPUnit\Framework\TestCase;
use oxTestModules;

class ResponseTest extends TestCase
{
    public function testSetData()
    {
        $idString = uniqid('', true);
        $data = [
            'id' => $idString,
        ];

        $response = oxNew(Response::class);
        $response = $response->setData($data);
        $this->assertInstanceOf(Response::class, $response);

        $responseArray = (array) $response;
        $this->assertEquals(200, $responseArray["\0*\0code"]);
        $this->assertEquals("200 OK", $responseArray["\0*\0status"]);
        $this->assertEquals($idString, $responseArray["\0*\0data"]['id']);
    }

    public function testSetGenericSuccess()
    {
        $response = oxNew(Response::class);
        $response = $response->setGenericSuccess();
        $this->assertInstanceOf(Response::class, $response);

        $responseArray = (array) $response;
        $this->assertEquals(200, $responseArray["\0*\0code"]);
        $this->assertEquals("200 OK", $responseArray["\0*\0status"]);
        $this->assertEquals("200 OK", $responseArray["\0*\0data"]['message']);
    }

    public function testSetUnauthorized()
    {
        $response = oxNew(Response::class);
        $response = $response->setUnauthorized();
        $this->assertInstanceOf(Response::class, $response);

        $responseArray = (array) $response;
        $this->assertEquals(401, $responseArray["\0*\0code"]);
        $this->assertEquals("401 Unauthorized ", $responseArray["\0*\0status"]);
        $this->assertEquals("401 Unauthorized ", $responseArray["\0*\0data"]['message']);
    }

    public function testSetForbidden()
    {
        $response = oxNew(Response::class);
        $response = $response->setForbidden();
        $this->assertInstanceOf(Response::class, $response);

        $responseArray = (array) $response;
        $this->assertEquals(403, $responseArray["\0*\0code"]);
        $this->assertEquals("403 Forbidden ", $responseArray["\0*\0status"]);
        $this->assertEquals("403 Forbidden ", $responseArray["\0*\0data"]['message']);
    }

    public function testSetNotFound()
    {
        $response = oxNew(Response::class);
        $response = $response->setNotFound();
        $this->assertInstanceOf(Response::class, $response);

        $responseArray = (array) $response;
        $this->assertEquals(404, $responseArray["\0*\0code"]);
        $this->assertEquals("404 Not Found", $responseArray["\0*\0status"]);
        $this->assertEquals("404 Not Found", $responseArray["\0*\0data"]['message']);
    }

    public function testSetNotAllowed()
    {
        $response = oxNew(Response::class);
        $response = $response->setNotAllowed();
        $this->assertInstanceOf(Response::class, $response);

        $responseArray = (array) $response;
        $this->assertEquals(405, $responseArray["\0*\0code"]);
        $this->assertEquals("405 Method not Allowed", $responseArray["\0*\0status"]);
        $this->assertEquals("405 Method not Allowed", $responseArray["\0*\0data"]['message']);
    }

    public function testSetServerError()
    {
        $response = oxNew(Response::class);
        $response = $response->setServerError();
        $this->assertInstanceOf(Response::class, $response);

        $responseArray = (array) $response;
        $this->assertEquals(500, $responseArray["\0*\0code"]);
        $this->assertEquals("500 Internal Server Error", $responseArray["\0*\0status"]);
        $this->assertEquals("500 Internal Server Error", $responseArray["\0*\0data"]['message']);
    }

    /**
     * @throws \Throwable
     * @throws \JsonException
     */
    public function testSendJson()
    {
        $utilsStub = $this->createPartialMock(Utils::class, ['showMessageAndExit']);
        $utilsStub->expects($this->once())
            ->method('showMessageAndExit')
            ->with($this->equalTo("{\n    \"message\": \"200 OK\"\n}"));

        Registry::set(Utils::class, $utilsStub);

        // set simple Status: 200 OK Response
        $response = oxNew(Response::class);
        $response = $response->setGenericSuccess();
        $response->sendJson();

        $header = Registry::get(Header::class)->getHeader();
        $this->assertContains("Cache-Control: no-cache\r\n", $header);
        $this->assertContains("Content-Type: application/json\r\n", $header);
        $this->assertContains("Status: 200 OK\r\n", $header);
    }
}
