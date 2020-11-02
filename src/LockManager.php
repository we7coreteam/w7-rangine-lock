<?php

namespace W7\Lock;

use Closure;
use W7\Lock\Exception\LockChannelNotSupport;
use W7\Lock\Handler\HandlerAbstract;

class LockManager {
	protected $lockerMap = [];
	protected $lockerResolverMap = [];
	protected $defaultChannel;

	public function __construct($defaultChannel = 'default') {
		$this->defaultChannel = $defaultChannel;
	}

	public function channel($name = 'default') : HandlerAbstract {
		return $this->getLocker($name);
	}

	protected function getLocker($channel) {
		if (empty($this->lockerMap[$channel])) {
			$resolver = $this->lockerResolverMap[$channel] ?? null;
			if (!$resolver) {
				throw new LockChannelNotSupport($channel);
			}

			$this->lockerMap[$channel] = new $resolver();
		}

		return $this->lockerMap[$channel];
	}

	public function registerLockerResolver($name, Closure $resolver) {
		$this->lockerResolverMap[$name] = $resolver;
	}

	public function __call($name, $arguments) {
		return $this->channel()->$name(...$arguments);
	}
}