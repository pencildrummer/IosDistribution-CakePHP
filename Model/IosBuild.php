<?php

App::uses('IOSDistributionAppModel', 'IosDistribution.Model');
App::import('IosDistribution.Vendor', 'CFPropertyList/classes/CFPropertyList/CFPropertyList');

/**
 * IosBuild Model
 *
 */
class IosBuild extends IOSDistributionAppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'title';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'title' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);
	
/*
 * Callback implementations
 */
 
 	public function beforeSave($options = array()) {
	 	
	 	$ipaTemp = $this->request->data['File']['tmp'];
	 	
	 	if (is_dir($ipaTemp)) {
	 		$zip = zip_open($ipaTemp);
		 	if ($zip) {
			 	while ($zip_entry = zip_read($zip)) {
				    $fileinfo = pathinfo(zip_entry_name($zip_entry));
				    if ($fileinfo['basename'] == "Info.plist") {
				    
				    	$infoPlist = new CFPropertyList($fileinfo['dirname'] . DS . $fileinfo['basename']);
				    	$infoPlist = $infoPlist->toArray();
				    	
				    	$this->data[$this->alias]['bundle_identifier'] = $infoPlist['CFBundleIdentifier'];
				    	$this->data[$this->alias]['app_name'] = $infoPlist['CFBundleDisplayName'];
				    	$this->data[$this->alias]['icon'] = ($infoPlist['CFBundleIconFile'] != "" ? $infoPlist['CFBundleIconFile'] : ( count($infoPlist['CFBundleIconFile']) > 0 ?$infoPlist['CFBundleIconFile'][0] : null));
				    
					}
				}
		 	}
	 	}
	 	
	 	//$this->readMetadata();
	 	
	 	// Get build name
	 	
	 	//$this->a
	 	
 	}
	
	public function afterSave($created, $options = array()) {
		
		// Create plist manifest if not provided
		
		if (empty($this->data[$this->alias]['plist_url'])) {
			
			$this->generateManifest();
			
		}
		
	}
	
	public function beforeDelete($cascade = true) {
		
		$this->read();
		
	}
	
	public function afterDelete() {
		
		$plistFile = new File($this->manifestPath());
		if ($plistFile->exists()) {
			$plistFile->delete();
		}
		
	}

/*
 * Plist generation
 *
 */

	private function generateManifest($data = null) {
		
		$ipa_url = Router::fullBaseUrl() . '/' . Inflector::underscore($this->plugin) . '/files/ipas/' . $this->ipaFilename();
		
		$plistView = new View();
		$plistView->set($this->data[$this->alias]);
		$plistView->set(compact('ipa_url'));
		
		$plistViewRender = $plistView->render('IosDistribution.IosBuilds/plistFile', false);
		$plistFile = new File($this->manifestPath(), true);
		
		if ($plistFile->write($plistViewRender)) {
			
			$plistUrl = Router::fullBaseUrl() . '/' . Inflector::underscore($this->plugin) . '/files/plists/' . $this->manifestFilename();
			
			$this->saveField('plist_url', $plistUrl);
			
		}
		
	}
	
	private function manifestPath() {
		return App::pluginPath($this->plugin) . 'webroot' . DS . 'files' . DS . $this->identifier . DS . basename($this->appName) . '.plist';
	}
	
	private function ipaPath() {
		return App::pluginPath($this->plugin) . 'webroot' . DS . 'files' . DS . $this->identifier . DS . basename($this->appName) . '.ipa';
	}

/*
 *  Certificate verification
 *
 */

//// TODO

/*
 * IPA Metadata extraction
 *
 */

//// TODO

	private function readMetadata() {
		$ipaPath = $this->ipaPath();
		
		if (is_dir($ipaPath)) {
			$zip = zip_open($ipaPath);
			if ($zip) {
				while ($zip_entry = zip_read($zip)) {
				    $fileinfo = pathinfo(zip_entry_name($zip_entry));
				    if ($fileinfo['basename'] == "Info.plist" || $fileinfo['basename'] == "iTunesArtwork") {
						$fp = fopen($fileinfo['basename'], "w");
				    	if (zip_entry_open($zip, $zip_entry, "r")) {
							$buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
							fwrite($fp,"$buf");
							zip_entry_close($zip_entry);
							fclose($fp);
						}
					}
				}
			}
			zip_close($zip);
		}
	}

/*
 * Dropbox implementation (for HTTPS manifest hosting)
 *
 */

//// TODO

}
