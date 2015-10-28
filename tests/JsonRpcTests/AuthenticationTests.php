<?php
namespace JsonRpcTests;

use imrannaqvi\JsonRpc\Server;
use PHPUnit_Framework_TestCase;

class AuthenticationTests extends PHPUnit_Framework_TestCase
{
	protected $serviceManager;

	public function setUp()
	{
		$serviceManagerGrabber = new ServiceManagerGrabber();
		$this->serviceManager = $serviceManagerGrabber->getServiceManager();
	}
	
	public function testWrongAuthentication()
	{
		$config = array(
			'methods' => array(
				'login-wrong-auth' => array(
					'model' => 'JsonRpcTests\Models\Authentication',
					'method' => 'login',
					'authentication_required' => false,
					'authentication' => 'wrong-auth'
				),
				'login' => array(
					'model' => 'JsonRpcTests\Models\Authentication',
					'method' => 'login',
					'authentication_required' => false,
					//'authentication' => 'wrong-auth'
				),
				'logout' => array(
					'model' => 'JsonRpcTests\Models\Authentication',
					'method' => 'logout',
				),
			),
			'authentication' => array(
				'default' => 'asdfsadf'
			),
			'authentication_required' => true
		);
		$server = new Server($config, $this->serviceManager);
		//login with wrong authentication
		$response = $server->handle($this->assembleRequest('login-wrong-auth', array(
			'username' => 'userabc',
			'password' => 'pass21345'
		)));
		$this->assertEquals($response['error'], 'authentication-not-found-as-service[wrong-auth]');
		//login with correct authentication
		$response = $server->handle($this->assembleRequest('login', array(
			'username' => 'userabc',
			'password' => 'pass21345'
		)));
		print_r($response);
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
		if($token) {
			$headers = new \Zend\Http\Headers();
			$headers->addHeader(\Zend\Http\Header\Authorization::fromString('Authorization: Token '.$token));
			$request->setHeaders($headers);
		}
		return $request->setPost(new \Zend\Stdlib\Parameters($post));
	}
}