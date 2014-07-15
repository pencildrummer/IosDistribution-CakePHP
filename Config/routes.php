<?php

	Router::connect('/ios/install/:token', array(
		'plugin' => 'ios_distribution',
		'controller' => 'ios_builds',
		'action' => 'install'
	), array(
		'pass' => array(
			'token'
		)
	));

	Router::connect('/ios/manifest/:token', array(
		'plugin' => 'ios_distribution',
		'controller' => 'ios_builds',
		'action' => 'manifest'
	), array(
		'pass' => array(
			'token'
		)
	));
	
	Router::connect('/ios/profile/:token', array(
		'plugin' => 'ios_distribution',
		'controller' => 'ios_builds',
		'action' => 'profile'
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
	
	Router::connect('/ios/build/:token', array(
		'plugin' => 'ios_distribution',
		'controller' => 'ios_builds',
		'action' => 'view'
	), array(
		'pass' => array(
			'token'
		)
	));
	
	Router::connect('/ios/build/:token/provisioning', array(
		'plugin' => 'ios_distribution',
		'controller' => 'ios_builds',
		'action' => 'add_provisioning_profile'
	), array(
		'pass' => array(
			'token'
		)
	));