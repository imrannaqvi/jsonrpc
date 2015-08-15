<?php
namespace imrannaqvi\JsonRpc;

use Zend\Authentication\Storage\StorageInterface;
/*use Zend\Session\Config\SessionConfig;
use Zend\Db\TableGateway\TableGateway;
use Zend\Session\SaveHandler\DbTableGateway;
use Zend\Session\SaveHandler\DbTableGatewayOptions;*/

class AuthenticationStorage implements StorageInterface
{
	protected $contents = null;
	protected $collection = null;
	
	function __construct(\Common\Collection\Collection $collection)
	{
		$this->collection = $collection;
	}
	
	public function read($header = false)
	{
		if($header && get_class($header) === 'Zend\Http\Header\Authorization') {
			$rd = $this->collection->fetchOne(array(
				'api_token' => substr($header->getFieldValue(), 6)
			));
			if($rd) {
				$this->contents = $rd;
			}
		}
		return $this->contents;
	}
	
	public function write($contents)
	{
		if(gettype($contents) === 'object') {
			do {
				$md5 = md5(time().rand());
				$rd = $this->collection->fetchOne(array(
					'api_token' => $md5
				));
			} while ($rd);
			$toc = date('Y-m-d H:i:s');
			$id = $contents->getProperty('id');
			if( $this->collection->updateById($id, array(
				'api_token' => $md5,
				'api_token_toc' => $toc
			))) {
				$contents->token = $md5;
				$contents->token_toc = $toc;
			}
			$this->contents = $contents;
			return $this->contents;
		}
	}
	
	public function isEmpty()
	{
		return !! $this->contents;
	}
	
	public function clear()
	{
		if(
			$this->contents && 
			array_key_exists('_id', $this->contents)
		) {
			$this->collection->updateById($this->contents['_id'], array(
				'api_token' => md5(time().rand())
			));
		}
		$this->contents = null;
	}
}