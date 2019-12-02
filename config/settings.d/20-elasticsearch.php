<?php

if ( getenv( 'MWDEV_ELASTICSEARCH' ) ) {
	wfLoadExtension( 'Elastica' );
	wfLoadExtension( 'CirrusSearch' );

	$wgCirrusSearchClusters = [
		'default' => [ '127.0.0.1' ],
	];
	$wgSearchType = 'CirrusSearch';
}

// @phpcs:disable Zend.Files.ClosingTag.NotAllowed
?>
