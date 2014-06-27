<?php

App::uses('AppHelper', 'View/Helper');

class IosHelper extends AppHelper {
	
	public $helpers = array(
		'Html'
	);
	
	public function link($title, $ipaName, $options = array()) {
		$urlTemplate = 'itms-services://?action=download-manifest&url=%s';
		
		if (is_array($ipaName)) {
			$build = $ipaName;
		} else {
			App::import('Model', 'IosDistribution.IosBuild');
			$IosBuild = new IosBuild();
			$build = $IosBuild->findByIpaFilename($ipaName);	
		}
		
		if (!empty($build)) {
			
			$url = sprintf($urlTemplate, $build['IosBuild']['plist_url']);
			
			return $this->Html->link($title, $url, $options);
			
		}
	}
	
}