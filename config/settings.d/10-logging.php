<?php

use MediaWiki\Logger\ConsoleSpi;

if ( !defined( 'STDERR' ) ) {
	define( 'STDERR', fopen( 'php://stderr', 'w' ) );
}

if ( !isset( $maintClass ) || ( isset( $maintClass ) && $maintClass !== 'PHPUnitMaintClass' ) ) {
	$wgMWLoggerDefaultSpi = [
		'class' => ConsoleSpi::class,
	];
}

// @phpcs:disable Zend.Files.ClosingTag.NotAllowed
?>
