<?php

namespace MediaWikiDevEnv\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class Install extends Command {

	use WithDocker;

	protected function configure() {
		$this->setName( 'install' );
		$this->addOption(
			'database-type',
			'db',
			InputOption::VALUE_REQUIRED,
			'Database type to use. Defaults to SQLite.',
			'sqlite'
		);
	}

	/** @inheritDoc */
	protected function execute( InputInterface $input, OutputInterface $output ) {
		if ( file_exists( 'LocalSettings.php' ) ) {
			rename( 'LocalSettings.php', 'LocalSettings.tmp' );
		}
		$files = glob( getcwd() . '/cache/*' );
		foreach ( $files as $file ) {
			if ( is_file( $file ) ) {
				unlink( $file );
			}
		}
		$dockerComposeCommand = $this->getDockerComposeCommand( $input );

		if ( $dockerComposeCommand ) {
			$this->runDockerCompose( $output, $dockerComposeCommand, [ 'up', '-d' ] );
		}

		$process = new Process( [
			'php',
			'maintenance/install.php',
			'--dbtype',
			$input->getOption( 'database-type' ),
			'--dbpath',
			getcwd() . '/cache',
			'--scriptpath',
			'',
			'--pass',
			'mediawikidevenv',
			'--server',
			'http://127.0.0.1:9412',
			'MediaWikiDevEnv',
			'Admin'
		] );
		$process->setTimeout( null );
		$process->setIdleTimeout( null );
		$process->run( function ( $type, $buffer ) use ( $output ) {
			$output->writeln( '<info>' . trim( $buffer ) . '</info>' );
		} );
		$settingsLoader = <<<'FILE_CONTENTS'
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$settingsFiles = glob( getcwd() . '/vendor/kostajh/mediawiki-dev-env/config/settings.d/*.php' );
foreach( $settingsFiles as $settingsFile ) {
	require_once $settingsFile;
}
FILE_CONTENTS;
		file_put_contents( getcwd() . '/LocalSettings.php', $settingsLoader, FILE_APPEND );

		return 0;
	}

}
