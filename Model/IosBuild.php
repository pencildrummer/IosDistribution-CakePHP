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
 
 	public function beforeValidate($options = array()) {
	 	
	 	if (!empty($this->data))
		 	$beforeData = $this->data;
		$this->read();
		$this->data[$this->alias] = array_merge($this->data[$this->alias], $beforeData[$this->alias]);
	 	
	 	// Create token
	 	
	 	if (empty($this->data[$this->alias]['token']))
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
			 	
			 	@unlink($ipaTempPath);
			 	
			 	unset($this->data[$this->alias]['ipa_file']);
		 	}
		 	
	 	}
	 	
	 	return true;
 	}
 
 	public function beforeSave($options = array()) {
	 	
	 	if (empty($this->data[$this->alias]['plist_url'])) {
			
			$this->generateManifest();
				
			$this->data[$this->alias]['plist_url'] = $this->manifestUrl();
		}
		
		return true;
	 	
 	}
	
	public function beforeDelete($cascade = true) {
		
		$this->read();
		
	}
	
	public function afterDelete() {
		
		App::uses('Folder', 'Utility');
		$folder = new Folder($this->appPath());
		$folder->delete();
		
	}
	
	public function afterFind($results, $primary = false) {
		
		// Get profiles
		
		if ($primary) {
			
			foreach ($results as &$result) {
				
				$result[$this->alias]['profiles'] = $this->checkProfiles($result);
				
			}
			
		}
		
		return $results;
	}
	
/*
 * Check provision profiles
 *
 */
 
 
 	public function getProfiles($data = null) {
	 	if (empty($data)) $data = $this->data;
	 	
	 	return glob($this->basePath($data).'*.mobileprovision');
 	}
 	
 	public function checkProfiles($data = null) {
	 	
	 	if (empty($data)) $data = $this->data;
	 	
	 	$profiles = array();
	 	
	 	foreach ($this->getProfiles($data) as $profile) {
			$profiles[basename($profile)] = ($this->checkProfile($profile, $data)) ? true : false;
	 	}
	 	
	 	return $profiles;
	 	
 	}
 	
 	public function checkProfile($profile, $data = null) {
 		if (empty($data)) $data = $this->data;
 		$companyIdentifier = pathinfo($data[$this->alias]['bundle_identifier'], PATHINFO_FILENAME);
	 	$contents = file_get_contents($profile);
	 	$search = strstr($contents, $companyIdentifier);
	 	$seek = strpos($search, "</string>");
	 	return ($seek!== false) ? true : false;
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
		return $this->appPath($data) . basename($data[$this->alias]['app_name']) . '.plist';
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
		return $this->appPath($data) . basename($data[$this->alias]['app_name']) . '.ipa';
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
	
	public function profilePath($data = null) {
		if (empty($data)) $data = $this->data;
		
		$profiles = $this->getProfiles($data);
		if (empty($profiles)) return '';
		return $profiles[0];
	}
	
	public function appPath($data = null) {
		if (empty($data)) $data = $this->data;
		return $this->basePath($data) . $data[$this->alias]['bundle_version'] . '_' . $data[$this->alias]['build_number'] . DS;
	}
	
	public function basePath($data = null) {
		if (empty($data)) $data = $this->data;
		return App::pluginPath($this->plugin) . 'files' . DS . $data[$this->alias]['bundle_identifier'] . DS;
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

				    if ($fileinfo['basename'] == "Info.plist" && preg_match('/^Payload\/[a-zA-Z0-9 ]*\.app$/', $fileinfo['dirname'])) {
						
				    	$fileTempPath = TMP . basename($ipaPath) . $fileinfo['basename'];
				    	
						$fp = fopen($fileTempPath, "w");
						
				    	if (zip_entry_open($zip, $zip_entry, "r")) {
							$buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
							fwrite($fp,"$buf");
							zip_entry_close($zip_entry);
							fclose($fp);
							
							$infoPlist = new CFPropertyList\CFPropertyList($fileTempPath);
							
					    	$infoPlist = $infoPlist->toArray();
							
							$this->data[$this->alias]['title'] = $infoPlist['CFBundleDisplayName'];
					    	$this->data[$this->alias]['bundle_identifier'] = $infoPlist['CFBundleIdentifier'];
					    	$this->data[$this->alias]['bundle_version'] = $infoPlist['CFBundleShortVersionString'];
					    	$this->data[$this->alias]['build_number'] = $infoPlist['CFBundleVersion'];
					    	$this->data[$this->alias]['app_name'] = $infoPlist['CFBundleDisplayName'];
					    	if (!empty($infoPlist['CFBundleIconFile']))
						    	$this->data[$this->alias]['icon'] = ($infoPlist['CFBundleIconFile'] != "" ? $infoPlist['CFBundleIconFile'] : ( count($infoPlist['CFBundleIconFile']) > 0 ?$infoPlist['CFBundleIconFile'][0] : null));
					    	
							@unlink($fileTempPath);
						}
						
					}
				}
			}
			zip_close($zip);
	}

}
