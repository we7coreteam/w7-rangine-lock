<?php

namespace W7\Lock\Handler;

class LuaScripts {
	public static function acquireLock() {
		return <<<'LUA'
if redis.call("setnx",KEYS[1], ARGV[1]) == 1 then
    return redis.call("expire",KEYS[1], ARGV[2])
else
    return 0
end
LUA;
	}

	public static function releaseLock() {
		return <<<'LUA'
if redis.call("get",KEYS[1]) == ARGV[1] then
    return redis.call("del",KEYS[1])
else
    return 0
end
LUA;
	}
}
