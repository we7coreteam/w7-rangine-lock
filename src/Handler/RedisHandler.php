<?php

/**
 * Rangine Lock
 *
 * (c) We7Team 2019 <https://www.rangine.com>
 *
 * document http://s.w7.cc/index.php?c=wiki&do=view&id=317&list=2284
 *
 * visited https://www.rangine.com for more details
 */

namespace W7\Lock\Handler;

use Psr\SimpleCache\CacheInterface;
use W7\Core\Facades\Cache;

class RedisHandler extends HandlerAbstract {
	/**
	 * @var \Redis
	 */
	protected $redis;

	public function __construct(CacheInterface $cache) {
		$this->redis = $cache;
	}

	public static function getHandler(array $config = []): HandlerAbstract {
		return new static(Cache::channel($config['redis_channel'] ?? 'default'));
	}

	/**
	 * @param $name
	 * @param $owner
	 * @param int $seconds
	 * @return bool
	 */
	public function acquire($name, $owner, $seconds = 0) {
		if ($seconds > 0) {
			return $this->redis->eval(LuaScripts::acquireLock(), [$name, $owner, $seconds], 1) == 1;
		} else {
			return $this->redis->setnx($name, $owner) == true;
		}
	}

	/**
	 * @param $name
	 * @param $owner
	 * @return bool|void
	 */
	public function release($name, $owner) {
		return (bool) $this->redis->eval(LuaScripts::releaseLock(), [$name, $owner], 1);
	}

	/**
	 * @param $name
	 *
	 * @return void
	 */
	public function forceRelease($name) {
		$this->redis->del($name);
	}

	/**
	 * @param $name
	 * @return bool|mixed|string
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function getCurrentOwner($name) {
		return $this->redis->get($name);
	}
}
