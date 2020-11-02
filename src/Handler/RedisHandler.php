<?php

namespace W7\Lock\Handler;

use Psr\SimpleCache\CacheInterface;
use W7\Core\Facades\Cache;

class RedisHandler extends HandlerAbstract {
	/**
	 * @var \Redis
	 */
	protected $redis;

	public function __construct(CacheInterface $cache, $name, $seconds, $owner = null) {
		parent::__construct($name, $seconds, $owner);
		$this->redis = $cache;
	}

	public static function getHandler($name, $seconds, $owner = null, array $config = []): HandlerAbstract {
		return new static(Cache::channel($config['redis_channel'] ?? null), $name, $seconds, $owner);
	}

	/**
	 * @return bool
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function acquire() {
		if ($this->seconds > 0) {
			return $this->redis->eval(LuaScripts::acquireLock(), [$this->name, $this->owner, $this->seconds], 1) == 1;
		} else {
			return $this->redis->setnx($this->name, $this->owner) == true;
		}
	}

	/**
	 * Release the lock.
	 *
	 * @return bool
	 */
	public function release() {
		return (bool) $this->redis->eval(LuaScripts::releaseLock(), [$this->name, $this->owner], 1);
	}

	/**
	 * Releases this lock in disregard of ownership.
	 *
	 * @return void
	 */
	public function forceRelease(){
		$this->redis->del($this->name);
	}

	/**
	 * @return mixed|string|null
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	protected function getCurrentOwner() {
		return $this->redis->get($this->name);
	}
}