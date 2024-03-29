<?php


namespace MediaWikiDevEnv\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class Serve extends Command {

	use WithDocker;

	protected function configure() {
		$this->setName( 'serve' );
		$this->setDefinition( new InputDefinition( [
			SharedOptions::redis(),
			new InputOption(
				'with-elasticsearch',
				'elasticsearch',
				InputOption::VALUE_OPTIONAL,
				'(docker-compose) If elasticsearch should be used.',
				false
			)
		] ) );
	}

	/** @inheritDoc */
	protected function execute( InputInterface $input, OutputInterface $output ) {
		$pidFile = getcwd() . '/.mwdevenv.pid';
		if ( file_exists( $pidFile ) ) {
			// TODO: Something safer.
			unlink( $pidFile );
			posix_kill( (int)file_get_contents( $pidFile ), SIGKILL );
		}
		$dotEnvFile = getcwd() . '/.mwdev.env';
		if ( file_exists( $dotEnvFile ) ) {
			unlink( $dotEnvFile );
		}
		$dotEnvContents = [];
		if ( $input->getOption( 'with-redis' ) ) {
			$dotEnvContents[] = 'MWDEV_REDIS=1';
		}
		if ( $input->getOption( 'with-elasticsearch' ) ) {
			$dotEnvContents[] = 'MWDEV_ELASTICSEARCH=1';
		}
		file_put_contents( $dotEnvFile, implode( "\n", $dotEnvContents ) );
		$dockerComposeCommand = $this->getDockerComposeCommand( $input );

		if ( $dockerComposeCommand ) {
			$output->writeln( sprintf(
				'<info>Running %s</info>', implode( ' ', $dockerComposeCommand ) )
			);
			$this->runDockerCompose( $output, $dockerComposeCommand, [ 'down' ] );
			$this->runDockerCompose( $output, $dockerComposeCommand, [ 'up', '-d' ] );
		}
		$process = new Process( [
			'php',
			'-d',
			'output_buffering=Off',
			'-S',
			'127.0.0.1:9412',
			'maintenance/dev/includes/router.php'
		] );
		$process->setTimeout( null );
		$process->setIdleTimeout( null );
		$process->run( function ( $type, $buffer ) use ( $output, $process ) {
			$output->writeln( '<info>' . trim( $buffer ) . '</info>' );
		} );
		file_put_contents( getcwd() . '/.mwdevenv.pid', $process->getPid() );
		// TODO: Stop docker-compose and remove the PID.
		return 0;
	}

}
