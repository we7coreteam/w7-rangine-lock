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

use W7\Core\Provider\ProviderAbstract;
use W7\Lock\Handler\RedisHandler;

class ServiceProvider extends ProviderAbstract {
	public function register() {
		$this->registerLockFactory();
	}

	protected function registerLockFactory() {
		$this->container->set('lock-factory', function () {
			$handlerConfig = $this->config->get('handler.lock', []);
			$handlerConfig['redis'] = RedisHandler::class;

			$config = $this->config->get('lock', []);
			$config['channel'] = $config['channel'] ?? [];
			if (empty($config['channel']['default'])) {
				$config['channel']['default'] = [
					'driver' => 'redis'
				];
			}

			foreach ($config['channel'] as $name => &$setting) {
				$setting['driver'] = $setting['driver'] ?? 'default';
				$setting['driver'] = $handlerConfig[$setting['driver']] ?? $setting['driver'];
			}

			return new LockFactory($config['channel'], $config['default'] ?? 'default');
		});
	}
}
