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
	public function view($id = null) {
		if (!$this->IosBuild->exists($id)) {
			throw new NotFoundException(__('Invalid ios build'));
		}
		$options = array('conditions' => array('IosBuild.' . $this->IosBuild->primaryKey => $id));
		$build = $this->IosBuild->find('first', $options);
		$build['IosBuild']['profiles'] = $this->IosBuild->checkProfile($build);
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
	
/**
 * download method
 *
 *
 */
 	public function download($token = null) {
 		$build = $this->IosBuild->findByToken($token);
 		
	 	$this->response->file($this->IosBuild->ipaPath());
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
	
}
