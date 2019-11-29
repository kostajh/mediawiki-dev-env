<?php

namespace MediaWikiDevEnv\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Process\Process;

trait WithDocker {

	private function useDocker( InputInterface $input ) {
		try {
			return $input->getOption( 'with-redis' );
		} catch ( \InvalidArgumentException $exception ) {
			return false;
		}
	}

	private function getDockerComposeCommand( InputInterface $input ) {
		$dockerComposeCommand = [];

		if ( $this->useDocker( $input ) ) {
			$dockerComposeCommand = [
				'docker-compose',
				'-f',
				'vendor/kostajh/mediawiki-dev-env/config/docker-compose/docker-compose.yml'
			];
			if ( $input->getOption( 'with-redis' ) ) {
				$dockerComposeCommand = array_merge( $dockerComposeCommand, [
					'-f',
					'vendor/kostajh/mediawiki-dev-env/config/docker-compose/redis/docker-compose.yml'
				] );
			}
		}
		return $dockerComposeCommand;
	}

	private function runDockerComposeUp( $command, $output ) {
		$command = array_merge( $command, [ 'up' ] );
		$process = new Process( $command );
		$process->setIdleTimeout( null );
		$process->setTimeout( null );
		$process->start();
		// TODO: Wait for health checks to finish?
		return $process;
	}

}
