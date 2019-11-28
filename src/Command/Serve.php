<?php


namespace MediaWikiDevEnv\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class Serve extends Command {

	protected function configure() {
		$this->setName( 'serve' );
	}

	/** @inheritDoc */
	protected function execute( InputInterface $input, OutputInterface $output ) {
		$process = new Process( [ 'php', '-d', 'output_buffering=Off', '-S', '127.0.0.1:9412' ] );
		$process->setTimeout( null );
		$process->setIdleTimeout( null );
		$process->run( function ( $type, $buffer ) use ( $output ) {
			$output->writeln( '<info>' . trim( $buffer ) . '</info>' );
		} );
		return 0;
	}

}
