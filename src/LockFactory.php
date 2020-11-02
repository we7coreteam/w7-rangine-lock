<?php

namespace W7\Lock;

use Closure;
use W7\Lock\Exception\LockChannelNotSupport;
use W7\Lock\Handler\HandlerAbstract;

class LockFactory {
	protected $defaultChannel;
	protected $channelsConfig;

	public function __construct($channelsConfig = [], $defaultChannel = 'default') {
		$this->channelsConfig = $channelsConfig;
		$this->defaultChannel = $defaultChannel;
	}

	public function setDefaultChannel($defaultChannel = 'default') {
		$this->defaultChannel = $defaultChannel;
	}

	public function channel($channel = 'default') : LockFactory {
		$lockFactory = clone $this;
		$lockFactory->setDefaultChannel($channel);

		return $lockFactory;
	}

	public function getLocker($name, $seconds, $owner = null) {
		$channel = $this->defaultChannel;
		if (empty($this->channelsConfig[$channel])) {
			throw new LockChannelNotSupport($channel);
		}

		/**
		 * @var HandlerAbstract $handler
		 */
		$handler = $this->channelsConfig[$channel]['driver'];
		return $handler::getHandler($name, $seconds, $owner, $this->channelsConfig[$channel]);
	}
}