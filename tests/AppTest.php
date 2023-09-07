<?php

namespace tests;

use App\Model\Employee;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{
    /**
     * @throws \Exception
     * @throws GuzzleException
     */
    public function testEmployeeListTest(): void
    {
        $request = new Client([
            'base_uri' => 'http://192.168.1.4:8000'
        ]);
        $res = $request->request('POST', '/list');

        $body = $res->getBody();

        $this->assertJson($body);
    }

    /**
     * @throws \Exception
     * @throws GuzzleException
     */
    public function testEmployeeAverageTest(): void
    {
        $request = new Client([
            'base_uri' => 'http://192.168.1.4:8000'
        ]);
        $res = $request->request('POST', '/salary');

        $body = $res->getBody();

        $this->assertJson($body);
    }
}