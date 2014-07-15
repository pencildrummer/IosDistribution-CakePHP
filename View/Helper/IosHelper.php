<?php

App::uses('AppHelper', 'View/Helper');

class IosHelper extends AppHelper {
	
	public $helpers = array(
		'Html'
	);
	
	public function install($title, $ipaName, $options = array()) {
		
		$build = $this->_getBuild($ipaName);
		
		if (empty($build)) return;
		
		return $this->Html->link($title, array(
			'plugin' => 'ios_distribution',
			'controller' => 'ios_builds',
			'action' => 'install',
			'token' => $build['IosBuild']['token']
		), $options);
		
	}
	
	public function profile($title, $ipaName, $options = array()) {
		
		$build = $this->_getBuild($ipaName);
		
		if (empty($build)) return;
		
		return $this->Html->link($title, array(
			'plugin' => 'ios_distribution',
			'controller' => 'ios_builds',
			'action' => 'profile',
			'token' => $build['IosBuild']['token']
		), $options);
	}
	
	public function link($title, $ipaName, $options = array()) {
		$urlTemplate = 'itms-services://?action=download-manifest&url=%s';
		
		$build = $this->_getBuild($ipaName);
		
		if (!empty($build)) {
			
			$url = sprintf($urlTemplate, $build['IosBuild']['plist_url']);
			
			return $this->Html->link($title, $url, $options);
			
		}
	}
	
	private function _getBuild($tokenOrBuild) {
		if (is_array($tokenOrBuild)) {
			$build = $tokenOrBuild;
		} else {
			App::import('Model', 'IosDistribution.IosBuild');
			$IosBuild = new IosBuild();
			$build = $IosBuild->findByToken($tokenOrBuild);	
		}
		return $build;
	}
	
}