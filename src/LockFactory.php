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

use W7\Lock\Exception\LockChannelNotSupportException;
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

	public function getLock($name, $seconds, $owner = null) {
		$channel = $this->defaultChannel;
		if (empty($this->channelsConfig[$channel])) {
			throw new LockChannelNotSupportException($channel);
		}

		/**
		 * @var HandlerAbstract $handler
		 */
		$handler = $this->channelsConfig[$channel]['driver'];
		$handler = $handler::getHandler($this->channelsConfig[$channel]);

		return new Lock($handler, $name, $seconds, $owner);
	}
}
