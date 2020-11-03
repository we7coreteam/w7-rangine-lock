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

namespace W7\Lock\Facade;

use W7\Core\Facades\FacadeAbstract;
use W7\Lock\Lock;

/**
 * Class LockFactory
 * @package W7\Lock\Facade
 *
 * @method static \W7\Lock\LockFactory channel($channel = 'default');
 * @method static Lock getLock($name, $seconds, $owner = null);
 */
class LockFactory extends FacadeAbstract {
	protected static function getFacadeAccessor() {
		return 'lock-factory';
	}
}
