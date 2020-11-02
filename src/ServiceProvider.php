<?php

namespace W7\Lock;

use W7\Core\Provider\ProviderAbstract;

class ServiceProvider extends ProviderAbstract {
	public function register() {

	}

	protected function registerLockManager() {
		$this->container->set('lock-factory', function () {
			$lockFactory = new LockFactory();


		});
	}
}