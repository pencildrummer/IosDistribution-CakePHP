<?php

App::uses('IosDistributionAppController', 'IosDistribution.Controller');

class IosDistributionController extends IosDistributionAppController {

	public $uses = array('IosBuild');

	public function index() {
		
		$builds = $this->IosBuild->find('all');
		
		$this->set(compact('builds'));
		
	}

}