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

abstract class HandlerAbstract {
	abstract public static function getHandler(array $config = []) : HandlerAbstract;

	/**
	 * @param $name
	 * @param $owner
	 * @param int $seconds
	 * @return bool
	 */
	abstract public function acquire($name, $owner, $seconds = 0);

	/**
	 * @param $name
	 * @param $owner
	 * @param int $seconds
	 * @return void
	 */
	public function wait($name, $owner, $seconds = 0) {
		usleep(250 * 1000);
	}

	/**
	 * Release the lock.
	 *
	 * @param $name
	 * @param $owner
	 *
	 * @return bool
	 */
	abstract public function release($name, $owner);

	/**
	 * Releases this lock in disregard of ownership.
	 *
	 * @param $name
	 *
	 * @return bool
	 */
	abstract public function forceRelease($name);

	/**
	 * Returns the owner value written into the driver for this lock.
	 *
	 * @param $name
	 *
	 * @return string
	 */
	abstract public function getCurrentOwner($name);
}
