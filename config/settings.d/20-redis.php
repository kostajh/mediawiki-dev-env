<?php

if ( getenv( 'MWDEV_REDIS' ) ) {
	if ( !class_exists( Redis::class ) ) {
		// todo: Show an error.
		return;
	}
	$wgObjectCaches['redis'] = [
		'class' => 'RedisBagOStuff',
		'servers' => [ '127.0.0.1:6379' ],
	];
	$wgMainCacheType = 'redis';
	$wgSessionCacheType = 'redis';
	$wgMainStash = 'redis';

	$wgJobTypeConf['default'] = [
		'class' => 'JobQueueRedis',
		'redisServer' => '127.0.0.1:6379',
		'redisConfig' => [],
		'claimTTL' => 3600,
		'daemonized' => true
	];
}
// @phpcs:disable Zend.Files.ClosingTag.NotAllowed
?>
