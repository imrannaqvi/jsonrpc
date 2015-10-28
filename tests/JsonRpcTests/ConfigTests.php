<?php
namespace JsonRpcTests;

use imrannaqvi\JsonRpc\Config;
use PHPUnit_Framework_TestCase;

class ConfigTests extends PHPUnit_Framework_TestCase
{
	protected $serviceManager;

	public function setUp()
	{
		$serviceManagerGrabber = new ServiceManagerGrabber();
		$this->serviceManager = $serviceManagerGrabber->getServiceManager();
	}
	
	public function testAuthenticationRequired()
	{
		//globally false
		$config = new Config(array(
			'methods' => array(
				'method1' => array(),
				'method2' => array(
					'authentication_required' => true
				),
			),
			//'authentication_required' => false
		));
		$details = $config->getMethodDetails('method1');
		$this->assertFalse($details['authentication_required']);
		$details = $config->getMethodDetails('method2');
		$this->assertTrue($details['authentication_required']);
		//globally true
		$config = new Config(array(
			'methods' => array(
				'method1' => array(),
				'method2' => array(
					'authentication_required' => false
				),
			),
			'authentication_required' => true
		));
		$details = $config->getMethodDetails('method1');
		$this->assertTrue($details['authentication_required']);
		$details = $config->getMethodDetails('method2');
		$this->assertFalse($details['authentication_required']);
	}
}