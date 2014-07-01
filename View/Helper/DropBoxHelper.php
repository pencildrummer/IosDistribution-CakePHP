<?php

App::uses('AppHelper', 'View/Helper');

class DropboxHelper extends AppHelper {
	
	public $helpers = array('Html');
	
	public function scripts($appKey = null, $options = array()) {
		
		if (empty($appKey))
			$appKey = Configure::read('IosDistribution.Dropbox.AppKey');
		
		$defaults = array(
			'id' => 'dropboxjs',
			'data-app-key' => $appKey
		);
		$options = array_merge($defaults, $options);
		$this->Html->script('https://www.dropbox.com/static/api/2/dropins.js', $options);
		
	}
	
	public function chooser($options = array()) {
		
		$defaults = array(
			'target' => ''
		);
		$options = array_merge($defaults, $options);
		
		$elementID = 'dropbox-chooser-'.String::uuid();
		$script = '
		var button = Dropbox.createChooseButton({
			success: function(files) {
				document.getElementById("'.$options['target'].'").value = files[0].link;
			},
			cancel : function() {
			},
			linkType : "direct",
			multiSelect : false,
			extensions : [".plist"] 
		});
		document.getElementById("'.$elementID.'").appendChild(button);
		';
		
		$out = $this->Html->tag('span', null, array('id' => $elementID));
		$out .= $this->Html->scriptBlock($script);
		
		return $out;
	}
	
	public function saver() {
		
	}
	
}