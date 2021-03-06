<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\Table;

/**
 * Movies Controller
 *
 * @property \App\Model\Table\MoviesTable $Movies
 */
class MoviesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['MovieDesc', 'People', 'MovieReviews']
        ];

        if(!empty($this->request->query)){

            foreach($this->request->query as $field => $query){
                // check the flags first
                if(is_array($query) || $field == 'page'){
                    continue;
                } else if (!empty($query)) {
                    if ($field == 'actor') {
                        $this->paginate['conditions']['actor_1'.' LIKE'] = '%'.$query.'%';
                    } else {
                        $this->paginate['conditions'][$field.' LIKE'] = '%'.$query.'%';
                    }
                }
            }
            $this->request->data = $this->request->query;
        }
        
        $movies = $this->paginate($this->Movies);

        $this->set(compact('movies'));
        $this->set('_serialize', ['movies']);
    }

    /**
     * View method
     *
     * @param string|null $id Movie id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $movie = $this->Movies->get($id, [
            'contain' => ['MovieDesc', 'People', 'MovieReviews']
        ]);

        $this->set('movie', $movie);
        $this->set('_serialize', ['movie']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $movie = $this->Movies->newEntity();
        if ($this->request->is('post')) {
            $movie = $this->Movies->patchEntity($movie, $this->request->getData());
            if ($this->Movies->save($movie)) {
                $this->Flash->success(__('The movie has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The movie could not be saved. Please, try again.'));
        }
        $movieDesc = $this->Movies->MovieDesc->find('list', ['limit' => 200]);
        $people = $this->Movies->People->find('list', ['limit' => 200]);
        $movieReviews = $this->Movies->MovieReviews->find('list', ['limit' => 200]);
        $this->set(compact('movie', 'movieDesc', 'people', 'movieReviews'));
        $this->set('_serialize', ['movie']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Movie id.
     * @return \Cake\Network\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $movie = $this->Movies->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $movie = $this->Movies->patchEntity($movie, $this->request->getData());
            if ($this->Movies->save($movie)) {
                $this->Flash->success(__('The movie has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The movie could not be saved. Please, try again.'));
        }
        $movieDesc = $this->Movies->MovieDesc->find('list', ['limit' => 200]);
        $people = $this->Movies->People->find('list', ['limit' => 200]);
        $movieReviews = $this->Movies->MovieReviews->find('list', ['limit' => 200]);
        $this->set(compact('movie', 'movieDesc', 'people', 'movieReviews'));
        $this->set('_serialize', ['movie']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Movie id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $movie = $this->Movies->get($id);
        if ($this->Movies->delete($movie)) {
            $this->Flash->success(__('The movie has been deleted.'));
        } else {
            $this->Flash->error(__('The movie could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
