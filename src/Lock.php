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

namespace W7\Lock;

use Illuminate\Support\InteractsWithTime;
use Illuminate\Support\Str;
use W7\Lock\Exception\LockTimeoutException;
use W7\Lock\Handler\HandlerAbstract;

class Lock implements \Illuminate\Contracts\Cache\Lock {
	use InteractsWithTime;

	protected $name;
	protected $seconds;
	protected $owner;
	/**
	 * @var HandlerAbstract
	 */
	protected $handler;

	/**
	 * Lock constructor.
	 * @param HandlerAbstract $handler
	 * @param $name
	 * @param $seconds
	 * @param null $owner
	 */
	public function __construct(HandlerAbstract $handler, $name, $seconds, $owner = null) {
		$this->handler = $handler;

		if (is_null($owner)) {
			$owner = Str::random();
		}
		$this->name = $name;
		$this->owner = $owner;
		$this->seconds = $seconds;
	}

	public function setHandler(HandlerAbstract $handler) {
		$this->handler = $handler;
	}

	public function getHandler() : HandlerAbstract {
		return $this->handler;
	}

	/**
	 * @return bool
	 */
	public function acquire() {
		return $this->getHandler()->acquire($this->name, $this->owner, $this->seconds);
	}

	/**
	 * @return bool|void
	 */
	public function release() {
		return $this->getHandler()->release($this->name, $this->owner);
	}

	/**
	 * @return bool|void
	 */
	public function forceRelease() {
		return $this->getHandler()->forceRelease($this->name);
	}

	/**
	 * @return string
	 */
	protected function getCurrentOwner() {
		return $this->getHandler()->getCurrentOwner($this->name);
	}

	/**
	 * @param null $callback
	 * @return bool|mixed
	 */
	public function get($callback = null) {
		$result = $this->acquire();

		if ($result && is_callable($callback)) {
			try {
				return $callback();
			} finally {
				$this->release();
			}
		}

		return $result;
	}

	/**
	 * @param int $seconds
	 * @param null $callback
	 * @return bool
	 * @throws LockTimeoutException
	 */
	public function block($seconds, $callback = null) {
		$starting = $this->currentTime();

		while (! $this->acquire()) {
			$this->getHandler()->wait($this->name, $this->owner, $seconds);

			if ($this->currentTime() - $seconds >= $starting) {
				throw new LockTimeoutException();
			}
		}

		if (is_callable($callback)) {
			try {
				return $callback();
			} finally {
				$this->release();
			}
		}

		return true;
	}

	/**
	 * @return string|null
	 */
	public function owner() {
		return $this->owner;
	}

	/**
	 * @return bool
	 */
	protected function isOwnedByCurrentProcess() {
		return $this->getCurrentOwner() === $this->owner;
	}
}
