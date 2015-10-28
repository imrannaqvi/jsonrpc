<?php
namespace JsonRpcTests\Models;

use imrannaqvi\JsonRpc\Model;

class Authentication extends Model
{
	public function login($params)
	{
		return $params['username'] . '-' . $params['password'];
	}
	
	public function logout($params)
	{
		return array();
	}
}