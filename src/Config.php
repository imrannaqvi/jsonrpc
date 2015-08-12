<?php
namespace imrannaqvi\JsonRpc;

class Config
{
	/** @var array Used for storing config array. */
	private $config = array();
	
	/** @var boolean Whether authentication is required for all method calls. */
	public $authenticationRequired = false;
	
	/**
	 * Constructor
	 *
	 * @param array $config
	 */
	function __construct($config)
	{
		$this->config = $config;
		if(
			array_key_exists('authentication_required', $this->config) &&
			$this->config['authentication_required']
		) {
			$this->authenticationRequired = true;
		}
	}
	
	/**
	 * To check if method exists in config or not.
	 *
	 * @param string $method Method name.
	 *
	 * @return boolean
	 */
	public function methodExists($method)
	{
		if(
			array_key_exists('methods', (array) $this->config) &&
			array_key_exists($method, (array) $this->config['methods'])
		) {
			return true;
		}
		return false;
	}
	
	/**
	 * Prepare a single method details before dispatch.
	 *
	 * @param array $item Settings item of a single method from config.
	 *
	 * @return array
	 */
	private function prepareMethodDetails($item)
	{
		if(! array_key_exists('authentication_required', $item)) {
			$item['authentication_required'] = $this->authenticationRequired;
		}
		return $item;
	}
	
	/**
	 * Get config details for a single method.
	 *
	 * @param string $method Settings item of a single method from config.
	 *
	 * @return array
	 */
	public function getMethodDetails($method)
	{
		if(! $this->methodExists($method)) {
			return null;
		}
		return $this->prepareMethodDetails($this->config['methods'][$method]);
	}
	
	/**
	 * Get Authentication Service name from item config.
	 *
	 * @param string $name service name.
	 *
	 * @return string
	 */
	public function getAuthenticationServiceName($name)
	{
		if(
			array_key_exists('authentication', $this->config) &&
			is_array($this->config['authentication']) &&
			array_key_exists($name, $this->config['authentication'])
		) {
			return $this->config['authentication'][$name];
		}
		return null;
	}
	
	public function getAllAuthenticationServiceNames()
	{
		if( array_key_exists('authentication', $this->config)) {
			return (array) $this->config['authentication'];
		}
		return array();
	}
	
	public function getAllMethodDetails($serviceLocator)
	{
		$doc = array();
		foreach($this->config['methods'] as $key => $value) {
			$doc[$key] = array(
				'authentication_required' => array_key_exists('authentication_required', $value)? $value['authentication_required'] : $this->authenticationRequired,
				//'value' => $value
			);
			//authentication
			if(array_key_exists('authentication', $value)) {
				$doc[$key]['authentication'] = $value['authentication'];
			}
			//form
			if(array_key_exists('form', $value)) {
				$params = array();
				$form = $serviceLocator->get($value['form']);
				$elements = $form->getElementsData();
				//$doc[$key]['elements'] = $elements;
				for($i=0; $i<count($elements); $i++) {
					$return = array();
					//type
					$type = false;
					if(
						array_key_exists('attributes', $elements[$i]) &&
						array_key_exists('type', $elements[$i]['attributes'])
					) {
						$type = $elements[$i]['attributes']['type'];
					}
					if( in_array($elements[$i]['attributes']['type'], array('submit'))) {
						continue;
					}
					$api_type = $type;
					if(
						array_key_exists('attributes', $elements[$i]) &&
						array_key_exists('api-type', $elements[$i]['attributes'])
					) {
						$api_type = $elements[$i]['attributes']['api-type'];
					}
					//required
					$required = false;
					if(
						array_key_exists('validation', $elements[$i]) &&
						array_key_exists('required', $elements[$i]['validation'])
					) {
						$required = $elements[$i]['validation']['required'];
					}
					//validations
					$validations = array();
					if(
						array_key_exists('validation', $elements[$i]) &&
						array_key_exists('validators', $elements[$i]['validation'])
					) {
						$validators = $elements[$i]['validation']['validators'];
						for($j=0; $j<count($validators); $j++) {
							$validations[$validators[$j]['name']] = $validators[$j]['options'];
						}
					}
					//
					$params[$elements[$i]['name']] = array(
						'required' => $required,
						'type' => $api_type
					);
					if(count($validations)) {
						$params[$elements[$i]['name']]['validations'] = $validations;
					}
				}
				$doc[$key]['params'] = $params;
			}
			if(array_key_exists('return', $value)) {
				$doc[$key]['return'] = $value['return'];
			}
		}
		return $doc;
	}
}