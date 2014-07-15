<?php
App::uses('IosDistributionAppController', 'IosDistribution.Controller');
/**
 * IosBuilds Controller
 *
 * @property IosBuild $IosBuild
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class IosBuildsController extends IosDistributionAppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session');

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->IosBuild->recursive = 0;
		$this->set('iosBuilds', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($token = null) {
		$build = $this->IosBuild->findByToken($token);
		if (empty($build)) {
			throw new NotFoundException(__('Invalid ios build'));
		}
		$this->set('iosBuild', $build);
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->IosBuild->create();
			if ($this->IosBuild->save($this->request->data)) {
				$this->Session->setFlash(__('The ios build has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The ios build could not be saved. Please, try again.'));
			}
		}
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->IosBuild->exists($id)) {
			throw new NotFoundException(__('Invalid ios build'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->IosBuild->save($this->request->data)) {
				$this->Session->setFlash(__('The ios build has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The ios build could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('IosBuild.' . $this->IosBuild->primaryKey => $id));
			$this->request->data = $this->IosBuild->find('first', $options);
		}
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->IosBuild->id = $id;
		if (!$this->IosBuild->exists()) {
			throw new NotFoundException(__('Invalid ios build'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->IosBuild->delete()) {
			$this->Session->setFlash(__('The ios build has been deleted.'));
		} else {
			$this->Session->setFlash(__('The ios build could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
	
	public function install($token = null) {
		
		$build = $this->IosBuild->findByToken($token);
		
		if (!empty($build)) {
			
			$this->set('iosBuild', $build);
			
		} else {
			$this->redirect(array('action' => 'index'));
		}
		
	}
	
/**
 * download method
 *
 *
 */
 	public function download($token = null) {
 		$build = $this->IosBuild->findByToken($token);
 		
 		$this->response->type('application/octet-stream');
	 	$this->response->file($this->IosBuild->ipaPath($build));
	 	return $this->response;	 	
 	}

/**
 * manifest method
 *
 *
 */ 	
 	public function manifest($token = null) {
 		$build = $this->IosBuild->findByToken($token);
 		
	 	$this->response->file($this->IosBuild->manifestPath($build));
	 	return $this->response;
 	}

/**
 * profile method
 *
 */
 	public function profile($token = null) {
	 	$build = $this->IosBuild->findByToken($token);
	 	
	 	$this->response->type('application/octet-stream');
	 	$this->response->file($this->IosBuild->profilePath($build), array(
	 		'download' => true
	 	));
	 	return $this->response;
 	} 	
 	
/**
 * add_provisioning_profile method
 *
 */
 	public function add_provisioning_profile($token) {
	 	
	 	$build = $this->IosBuild->findByToken($token);
	 	
	 	if ($this->request->is('post')) {
		 	
		 	if (!empty($this->request->data['provisioning_profile']) && $this->request->data['provisioning_profile']['error'] == UPLOAD_ERR_OK) {
			 	
			 	$profileTempFile = new File($this->request->data['provisioning_profile']['tmp_name'], true);
			 	
			 	$profilePath = $this->IosBuild->basePath($build) . $this->request->data['provisioning_profile']['name'];
			 	
			 	if ($profileTempFile->copy($profilePath)) {
			 		
			 		if ($this->IosBuild->checkProfile($profilePath, $build)) {
				 		$this->Session->setFlash(__('New provisioning profile uploaded'));	
			 		} else {
				 		$this->Session->setFlash(__('The provisioning profile uploaded is not valid for this build!'));
				 		@unlink($profilePath);
			 		}
			 		
					$this->redirect(array('action' => 'view', 'token' => $token)); 	
			 	}
			 	
		 	} else {
			 	$this->Session->setFlash(__('Error uploading provisioning profile'));
		 	}
	 	}
	 	
	 	$this->set('iosBuild', $build);
 	}
	
}
