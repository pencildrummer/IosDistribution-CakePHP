<?php

	Router::connect('/ios/manifest/:token', array(
		'plugin' => 'ios_distribution',
		'controller' => 'ios_builds',
		'action' => 'manifest'
	), array(
		'pass' => array(
			'token'
		)
	));
	
	Router::connect('/ios/download/:token', array(
		'plugin' => 'ios_distribution',
		'controller' => 'ios_builds',
		'action' => 'download'
	), array(
		'pass' => array(
			'token'
		)
	));