<?php
class TransactionsController extends AppController {
    
	public $helpers = array('Html', 'Form');

    public function index() {

        $this->set('transactions', $this->Transaction->find('all'));
    }
	
    public function view($id = null) {
        if (!$id) {
            throw new NotFoundException(__('Invalid transaction'));
        }

        $transaction = $this->Transaction->findById($id);
        if (!$transaction) {
            throw new NotFoundException(__('Invalid post'));
        }
        $this->set('transactions', $transaction);
    }
	
	public function add() {
        if ($this->request->is('post')) {
			$this->request->data['Transaction']['user_id'] = $this->Auth->user('id');
            $this->Transaction->create();
            if ($this->Transaction->save($this->request->data)) {
                $this->Session->setFlash('Your transaction has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Unable to add your transaction.');
            }
        }
    }
	
	public function edit($id = null) {
		if (!$id) {
			throw new NotFoundException(__('Invalid transaction'));
		}
	
		$transaction = $this->Transaction->findById($id);
		if (!$transaction) {
			throw new NotFoundException(__('Invalid transaction'));
		}
	
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->Transaction->id = $id;
			if ($this->Transaction->save($this->request->data)) {
				$this->Session->setFlash('Your transaction has been updated.');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('Unable to update your transaction.');
			}
		}
	
		if (!$this->request->data) {
			$this->request->data = $transaction;
		}
	}
	
	public function delete($id) {
		if ($this->request->is('get')) {
			throw new MethodNotAllowedException();
		}

		if ($this->Transaction->delete($id)) {
			$this->Session->setFlash('The post with id: ' . $id . ' has been deleted.');
			$this->redirect(array('action' => 'index'));
		}
	}
	
	public function isAuthorized($user) {
    // All registered users can add posts
		if ($this->action === 'add') {
			return true;
		}

    // The owner of a post can edit and delete it
		if (in_array($this->action, array('edit', 'delete'))) {
			$transactionsId = $this->request->params['pass'][0];
			if ($this->Transactions->isOwnedBy($transactionsId, $user['id'])) {
				return true;
			}
		}

		return parent::isAuthorized($user);
	}
}
?>