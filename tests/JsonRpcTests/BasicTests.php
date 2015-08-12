<?php
namespace JsonRpcTests;

use PHPUnit_Framework_TestCase;

class BasicTests extends PHPUnit_Framework_TestCase
{
	protected $serviceManager;

	public function setUp()
	{
		$serviceManagerGrabber = new ServiceManagerGrabber();
		$this->serviceManager = $serviceManagerGrabber->getServiceManager();
	}
	
	public function testEmptyConfig()
	{
		$config = array();
		$request = new \Zend\Http\PhpEnvironment\Request();
		$server = new \imrannaqvi\JsonRpc\Server($config, $this->serviceManager);
		$response = $server->handle($request);
		$this->assertArrayHasKey('response', $response);
		$this->assertNull($response['response']);
		$this->assertArrayHasKey('error', $response);
		$this->assertInternalType('string', $response['error']);
		$this->assertArrayHasKey('method', $response);
		$this->assertInternalType('string', $response['method']);
	}
}