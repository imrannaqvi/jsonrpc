<?php
namespace JsonRpcTests\Models;

use imrannaqvi\JsonRpc\Model;

class Basic extends Model
{
	public function add($params)
	{
		return (int) $params['a'] + (int) $params['b'];
	}
}