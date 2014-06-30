<?php

App::uses('IOSDistributionAppModel', 'IosDistribution.Model');
App::import('Vendor', 'IosDistribution.CFPropertyList', array(
	'file' => 'CFPropertyList' . DS . 'classes' . DS . 'CFPropertyList' . DS . 'CFPropertyList.php'
));

/*
	https://github.com/wbroek/IPA-Distribution/blob/master/ipaDistrubution.php
*/

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
	 	
	 	// Copy IPA
	 	/// TODO - Async request
	 	
	 	if ($this->data[$this->alias]['ipa_file']['error'] == UPLOAD_ERR_OK) {
		 	
		 	$ipaTemp = $this->data[$this->alias]['ipa_file']['tmp_name'];
		 	
		 	$ipaFile = new File($ipaTemp, true);
		 	$ipaTempPath = TMP . basename($ipaTemp) . time();
		 	if ($ipaFile->copy($ipaTempPath)) {
			 	
			 	$this->readMetadata($ipaTempPath);
			 	$this->data[$this->alias]['ipa_filename'] = $this->data[$this->alias]['app_name'] . '.ipa';
			 	
			 	$folder = new Folder(App::pluginPath('IosDistribution') . 'files' . DS . $this->data[$this->alias]['bundle_identifier'], true);
			 	$ipaFile->copy($folder->path . DS . $this->data[$this->alias]['ipa_filename']);
			 	
			 	unlink($ipaTempPath);
			 	
			 	unset($this->data[$this->alias]['ipa_file']);
		 	}
		 	
	 	}
	 	
	 	
 		/**/
	 	
	 	debug($this->data);
	 	
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
		
		//$ipa_url = Router::fullBaseUrl() . '/' . Inflector::underscore($this->plugin) . '/files/ipas/' . $this->ipaFilename();
		
		$plistView = new View();
		$plistView->set($this->data[$this->alias]);
		$plistView->set('ipa_url', $this->ipaUrl);
		
		$plistViewRender = $plistView->render('IosDistribution.IosBuilds/plistFile', false);
		$plistFile = new File($this->manifestPath(), true);
		
		if ($plistFile->write($plistViewRender)) {
			
			$plistUrl = Router::fullBaseUrl() . '/' . 'download_build' . '/' . pathinfo($this->manifestPath(), PATHINFO_BASENAME);
			
			$this->saveField('plist_url', $plistUrl, array('callbacks' => false));
			
		}
		
	}
	
	private function manifestPath() {
		return App::pluginPath($this->plugin) . 'webroot' . DS . 'files' . DS . $this->data[$this->alias]['bundle_identifier'] . DS . basename($this->data[$this->alias]['app_name']) . '.plist';
	}
	
	private function ipaPath() {
		return App::pluginPath($this->plugin) . 'webroot' . DS . 'files' . DS . $this->data[$this->alias]['bundle_identifier'] . DS . basename($this->data[$this->alias]['app_name']) . '.ipa';
	}
	
	private function ipaUrl() {
		return Router::fullBaseUrl() . '/' . 'ipa' . '/' . pathinfo($this->ipaPath, PATHINFO_BASENAME);
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

	private function readMetadata($ipaPath = null) {
		if (empty($ipaPath))
			$ipaPath = $this->ipaPath();
		
			$zip = zip_open($ipaPath);
			if ($zip) {
				while ($zip_entry = zip_read($zip)) {
				    $fileinfo = pathinfo(zip_entry_name($zip_entry));
				    if ($fileinfo['basename'] == "Info.plist" /*|| $fileinfo['basename'] == "iTunesArtwork"*/) {
				    
				    	$fileTempPath = TMP . basename($ipaPath) . $fileinfo['basename'];
				    	
						$fp = fopen($fileTempPath, "w");
						
				    	if (zip_entry_open($zip, $zip_entry, "r")) {
							$buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
							fwrite($fp,"$buf");
							zip_entry_close($zip_entry);
							fclose($fp);
							
							$infoPlist = new CFPropertyList\CFPropertyList($fileTempPath);
							
					    	$infoPlist = $infoPlist->toArray();
							
					    	$this->data[$this->alias]['bundle_identifier'] = $infoPlist['CFBundleIdentifier'];
					    	$this->data[$this->alias]['bundle_version'] = $infoPlist['CFBundleVersion'];
					    	$this->data[$this->alias]['app_name'] = $infoPlist['CFBundleDisplayName'];
					    	$this->data[$this->alias]['icon'] = ($infoPlist['CFBundleIconFile'] != "" ? $infoPlist['CFBundleIconFile'] : ( count($infoPlist['CFBundleIconFile']) > 0 ?$infoPlist['CFBundleIconFile'][0] : null));
					    	
							unlink($fileTempPath);
						}
						
					}
				}
			}
			zip_close($zip);
	}

/*
 * Dropbox implementation (for HTTPS manifest hosting)
 *
 */

//// TODO

}
