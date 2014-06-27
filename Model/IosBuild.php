<?php
App::uses('IOSDistributionAppModel', 'IosDistribution.Model');
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
	
	public function afterSave($created, $options = array()) {
		
		if (empty($this->data[$this->alias]['plist_url'])) {
			
			$this->generatePlist();
			
		}
		
	}
	
	public function beforeDelete($cascade = true) {
		
		$this->read();
		
	}
	
	public function afterDelete() {
		
		$plistFile = new File($this->plistPath());
		if ($plistFile->exists()) {
			$plistFile->delete();
		}
		
	}

/*
 * Plist generation
 *
 */

	private function generatePList($data = null) {
		
		$ipa_url = Router::fullBaseUrl() . '/' . Inflector::underscore($this->plugin) . '/files/ipas/' . $this->data[$this->alias]['ipa_filename'];
		
		$plistView = new View();
		$plistView->set($this->data[$this->alias]);
		$plistView->set(compact('ipa_url'));
		
		$plistViewRender = $plistView->render('IosDistribution.IosBuilds/plistFile', false);
		$plistFile = new File($this->plistPath(), true);
		
		if ($plistFile->write($plistViewRender)) {
			
			$plistUrl = Router::fullBaseUrl() . '/' . Inflector::underscore($this->plugin) . '/files/plists/' . $this->plistFilename();
			
			$this->saveField('plist_url', $plistUrl);
			
		}
		
	}
	
	private function plistFilename() {
		return $this->data[$this->alias][$this->primaryKey] . '.plist';
	}
	
	private function plistPath() {
		return App::pluginPath($this->plugin) . 'webroot' . DS . 'files' . DS . 'plists' . DS . $this->plistFilename();
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

/*
 * Dropbox implementation (for HTTPS manifest hosting)
 *
 */

//// TODO

}
