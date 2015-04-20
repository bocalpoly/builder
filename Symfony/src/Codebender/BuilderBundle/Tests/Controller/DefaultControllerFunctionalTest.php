<?php
/**
 * Created by PhpStorm.
 * User: fpapadopou
 * Date: 4/20/15
 * Time: 12:07 PM
 */

namespace Codebender\BuilderBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class DefaultControllerFunctionalTest extends WebTestCase
{
    public function testStatusAction() {
        $client = static::createClient();

        $client->request('GET', '/status');

        $this->assertEquals($client->getResponse()->getContent(), '{"success":true,"status":"OK"}');
    }

    public function testHandleRequestGet() {
        $client = static::createClient();

        /*
         * Using the same auth key and version like the ones in the dist parameter file
         */
        $client->request('GET', '/authKey/v1');

        $this->assertEquals($client->getResponse()->getStatusCode(), 405);
    }

    public function testHandleRequestCompile() {
        $client = static::createClient();

        $client
            ->request(
                'POST',
                'authenticationekey/version',
                $parameters = array(),
                $files = array(),
                $server = array(),
                $content = '{"type":"compiler","data":{"files":[{"filename":"project.ino","content":"void setup(){\n\n}\nvoid loop(){\n\n}\n"}],"format":"binary","version":"105","build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}}}',
                $changeHistory = true);

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('success', $response);
        $this->assertEquals($response['success'], true);
        $this->assertArrayHasKey('time', $response);
    }

    public function testHandleRequestLibraryFetching() {
        $client = static::createClient();

        $client
            ->request(
                'POST',
                'authenticationekey/version',
                $parameters = array(),
                $files = array(),
                $server = array(),
                $content = '{"type":"library","data":{"type":"fetch","library":"Ethernet"}}',
                $changeHistory = true);

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('success', $response);
        $this->assertEquals($response['success'], true);
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals($response['message'], 'Library found');
    }

    public function testHandleRequestLibraryKeywords() {
        $client = static::createClient();

        $client
            ->request(
                'POST',
                'authenticationekey/version',
                $parameters = array(),
                $files = array(),
                $server = array(),
                $content = '{"type":"library","data":{"type":"getKeywords","library":"Ethernet"}}',
                $changeHistory = true);

        $response = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('success', $response);
        $this->assertEquals($response['success'], true);
        $this->assertArrayHasKey('keywords', $response);
        $this->assertTrue(is_array($response['keywords']));
    }
}