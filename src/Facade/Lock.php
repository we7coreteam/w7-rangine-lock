<?php

namespace W7\Lock\Facade;

use W7\Core\Facades\FacadeAbstract;

class Lock extends FacadeAbstract{
	protected static function getFacadeAccessor() {
		return 'lock-manager';
	}
}