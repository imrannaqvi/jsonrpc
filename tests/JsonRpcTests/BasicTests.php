<?php
namespace JsonRpcTests;

use imrannaqvi\JsonRpc\Server;
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
		$server = new Server($config, $this->serviceManager);
		$response = $server->handle(new \Zend\Http\PhpEnvironment\Request());
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
		$server = new Server($config, $this->serviceManager);
		$response = $server->handle($this->assembleRequest($method));
		$this->assertArrayHasKey('method', $response);
		$this->assertInternalType('string', $response['method']);
		$this->assertEquals($response['method'], $method);
	}
	
	public function testConfigWithMethodInRequest()
	{
		$method = 'login';
		$config = array(
			'methods' => array(
				'login' => array(
					'model' => 'this/service/isnot/registered',
				),
			),
		);
		$server = new Server($config, $this->serviceManager);
		$response = $server->handle($this->assembleRequest($method));
		$this->assertArrayHasKey('method', $response);
		$this->assertInternalType('string', $response['method']);
		$this->assertEquals($response['method'], $method);
		$this->assertArrayHasKey('error', $response);
		$this->assertEquals($response['error'], 'model-not-found-as-service');
	}
	
	public function testConfigWithMethodAndModel()
	{
		//without method name in request
		$method = 'login';
		$config = array(
			'methods' => array(
				'login' => array(
					'model' => 'JsonRpcTests\Models\Basic',
				),
			),
		);
		$server = new Server($config, $this->serviceManager);
		$response = $server->handle($this->assembleRequest($method));
		$this->assertArrayHasKey('error', $response);
		$this->assertEquals($response['error'], 'model-method-not-defined');
		//with wrong method name in request
		$config = array(
			'methods' => array(
				'login' => array(
					'model' => 'JsonRpcTests\Models\Basic',
					'method' => 'asdf'
				),
			),
		);
		$server = new Server($config, $this->serviceManager);
		$response = $server->handle($this->assembleRequest($method));
		$this->assertArrayHasKey('error', $response);
		$this->assertEquals($response['error'], 'model-method-not-found');
		//with correct method name in request
		$method = 'add';
		$config = array(
			'methods' => array(
				'add' => array(
					'model' => 'JsonRpcTests\Models\Basic',
					'method' => 'add'
				),
			),
		);
		$server = new Server($config, $this->serviceManager);
		$response = $server->handle($this->assembleRequest($method, array(
			'a' => 1,
			'b' => 2
		)));
		$this->assertArrayHasKey('response', $response);
		$this->assertEquals($response['response'], 3);
	}
	
	public function assembleRequest($method = null, $params = array(), $token = null)
	{
		$request = new \Zend\Http\PhpEnvironment\Request();
		$request->setMethod(\Zend\Http\PhpEnvironment\Request::METHOD_POST);
		$post = array();
		if($method) {
			$post['method'] = $method;
		}
		if(is_array($params) && count($params)) {
			$post['params'] = $params;
		}
		return $request->setPost(new \Zend\Stdlib\Parameters($post));
	}
}