<?php
namespace imrannaqvi\JsonRpc;

use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\ServiceLocatorInterface;

class Model
{
	protected $authentication;
	protected $serviceLocator;
	protected $mvcController;
	
	public function __construct(ServiceLocatorInterface $serviceLocator)
	{
		$this->serviceLocator = $serviceLocator;
	}
	
	public function setAuthenticationService(AuthenticationService $authentication)
	{
		$this->authentication = $authentication;
	}
	
	public function getServiceLocator()
	{
		return $this->serviceLocator;
	}

	public function getMvcController()
	{
		return $this->mvcController;
	}

	public function setMvcController($mvcController)
	{
		$this->mvcController = $mvcController;
	}
}