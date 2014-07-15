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
				// Force direct link from preview link
				// Forcing direct link from preview link is needed
				// because "direct" links expires after 4 hours
				
				var link = files[0].link.replace("//www", "//dl");
				document.getElementById("'.$options['target'].'").value = link;
			},
			cancel : function() {
			},
			linkType : "preview",
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