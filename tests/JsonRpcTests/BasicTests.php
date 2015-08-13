<?php
namespace JsonRpcTests;

use imrannaqvi\JsonRpc\Server;
use PHPUnit_Framework_TestCase;
use Zend\Http\PhpEnvironment\Request;
use Zend\Stdlib\Parameters;

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
		$request = new Request();
		$server = new Server($config, $this->serviceManager);
		$response = $server->handle($request);
		$this->assertArrayHasKey('response', $response);
		$this->assertNull($response['response']);
		$this->assertArrayHasKey('error', $response);
		$this->assertInternalType('string', $response['error']);
		$this->assertEquals($response['error'], 'method-not-found');
		$this->assertArrayHasKey('method', $response);
		$this->assertInternalType('string', $response['method']);
		$this->assertEquals($response['method'], '');
	}
	
	public function testEmptyConfigWithMethodInRequest()
	{
		$method = 'asdf';
		$config = array();
		$request = new Request();
		$request->setMethod(Request::METHOD_POST);
		$request->setPost(new Parameters(array(
			'method' => $method
		)));
		$server = new Server($config, $this->serviceManager);
		$response = $server->handle($request);
		$this->assertArrayHasKey('method', $response);
		$this->assertInternalType('string', $response['method']);
		$this->assertEquals($response['method'], $method);
	}
}