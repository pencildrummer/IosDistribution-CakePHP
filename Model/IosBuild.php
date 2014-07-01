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
	 	
	 	$this->read();
	 	
	 	// Create token
	 	
	 	$this->data[$this->alias]['token'] = String::uuid();
	 	
	 	// Copy IPA
	 	/// TODO - Async request
	 	
	 	if (!empty($this->data[$this->alias]['ipa_file']) && $this->data[$this->alias]['ipa_file']['error'] == UPLOAD_ERR_OK) {
		 	
		 	$ipaTemp = $this->data[$this->alias]['ipa_file']['tmp_name'];
		 	
		 	$ipaFile = new File($ipaTemp, true);
		 	$ipaTempPath = TMP . basename($ipaTemp) . time();
		 	if ($ipaFile->copy($ipaTempPath)) {
			 	
			 	$this->readMetadata($ipaTempPath);
			 	$this->data[$this->alias]['ipa_filename'] = $this->data[$this->alias]['app_name'] . '.ipa';
			 	
			 	// Copy build to app folder
			 	
			 	$ipaPath = $this->ipaPath();
			 	$folder = new Folder(dirname($ipaPath), true);
			 	$ipaFile->copy($ipaPath);
			 	
			 	unlink($ipaTempPath);
			 	
			 	unset($this->data[$this->alias]['ipa_file']);
		 	}
		 	
	 	}
	 	
	 	if (empty($this->data[$this->alias]['plist_url'])) {
			
			$this->generateManifest();
				
			$this->data[$this->alias]['plist_url'] = $this->manifestUrl();
		}
	 	
 	}
	
	public function beforeDelete($cascade = true) {
		
		$this->read();
		
	}
	
	public function afterDelete() {
		
		App::uses('Folder', 'Utility');
		$folder = new Folder($this->basePath());
		$folder->delete();
		
	}
	
/*
 * Check provision profiles
 *
 */

 	public function checkProfile($data = null) {
	 	
	 	if (empty($data)) $data = $this->data;
	 	
	 	$companyIdentifier = pathinfo($data[$this->alias]['bundle_identifier'], PATHINFO_FILENAME);
	 	$profiles = array();
	 	
	 	foreach (glob($this->basePath($data).'*.mobileprovision') as $profile) {
		 	$contents = file_get_contents($profile);
		 	$search = strstr($contents, $companyIdentifier);
		 	$seek = strpos($search, "</string>");
			if ($seek!== false)
				$profiles[substr($search, 0, $seek)] = basename($profile);
	 	}
	 	
	 	if (empty($profiles)) return false;
	 	return $profiles;
	 	
 	}

/*
 * Plist generation
 *
 */

	private function generateManifest($data = null) {
		
		if (empty($data)) $data = $this->data;
		
		$plistView = new View();
		$plistView->set($this->data[$this->alias]);
		$plistView->set('ipa_url', $this->ipaUrl());
		
		$plistViewRender = $plistView->render('IosDistribution.IosBuilds/plistFile', false);
		$plistFile = new File($this->manifestPath(), true);
		
		$plistFile->write($plistViewRender);
		
	}
	
/*
 * Path and URL methods
 *
 */
	
	public function manifestPath($data = null) {
		if (empty($data)) $data = $this->data;
		return $this->basePath($data) . basename($data[$this->alias]['app_name']) . '.plist';
	}
	
	public function manifestUrl($data = null) {
		if (empty($data)) $data = $this->data;
		return Router::url(array(
			'plugin' => 'ios_distribution',
			'controller' => 'ios_builds',
			'action' => 'manifest',
			'token' => $data[$this->alias]['token']
		), true);
	}
	
	public function ipaPath($data = null) {
		if (empty($data)) $data = $this->data;
		return $this->basePath($data) . basename($data[$this->alias]['app_name']) . '.ipa';
	}
	
	public function ipaUrl($data = null) {
		if (empty($data)) $data = $this->data;
		return Router::url(array(
			'plugin' => 'ios_distribution',
			'controller' => 'ios_builds',
			'action' => 'download',
			$data[$this->alias]['token']
		), true);
	}
	
	protected function basePath($data = null) {
		if (empty($data)) $data = $this->data;
		return App::pluginPath($this->plugin) . 'files' . DS . $data[$this->alias]['bundle_identifier'] . DS . $data[$this->alias]['bundle_version'] . DS;
	}

/*
 * IPA Metadata extraction
 *
 */

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
