<?php

namespace MediaWikiDevEnv\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

trait WithDocker {

	private function useDocker( InputInterface $input ) {
		try {
			return $input->getOption( 'with-redis' ) ||
				$input->getOption( 'with-elasticsearch' );
		} catch ( \InvalidArgumentException $exception ) {
			return false;
		}
	}

	private function getDockerComposeCommand( InputInterface $input ) {
		$dockerComposeCommand = [];
		$configBasePath = 'vendor/kostajh/mediawiki-dev-env/config/docker-compose';

		if ( $this->useDocker( $input ) ) {
			$dockerComposeCommand = [
				'docker-compose',
				'-f',
				$configBasePath . '/docker-compose.yml'
			];
			if ( $input->getOption( 'with-redis' ) ) {
				$dockerComposeCommand = array_merge( $dockerComposeCommand, [
					'-f',
					$configBasePath . '/redis/docker-compose.yml'
				] );
			}
			if ( $input->getOption( 'with-elasticsearch' ) ) {
				$dockerComposeCommand = array_merge( $dockerComposeCommand, [
					'-f',
					$configBasePath . '/elasticsearch/docker-compose.yml'
				] );
			}
		}
		return $dockerComposeCommand;
	}

	private function runDockerCompose( OutputInterface $output, $command, $arguments ) {
		$command = array_merge( $command, $arguments );
		$process = new Process( $command );
		$process->setIdleTimeout( null );
		$process->setTimeout( null );
		$process->start();
		// TODO: Wait for health checks to finish?
		return $process;
	}

}
