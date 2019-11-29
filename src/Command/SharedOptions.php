<?php

namespace MediaWikiDevEnv\Command;

use Symfony\Component\Console\Input\InputOption;

class SharedOptions {

	public static function redis() {
		return new InputOption(
			'with-redis',
			'redis',
			InputOption::VALUE_OPTIONAL,
			'(docker-compose). If redis should be used',
			false
		);
	}
}
