<?php
namespace Kristins\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Kristins\Model\ServiceTicket;          // <-- Add this import
use Kristins\Model\ServiceTicketTable; 

class ServiceTicketController extends AbstractActionController
{
	protected $serviceTicket;
	
public function indexAction()
    {
        return new ViewModel(array(
            'albums' => $this->getServiceTicketTable()->fetchAll(),
        ));
    }

   public function addAction()
    {
        $form = new AlbumForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $album = new Album();
            $form->setInputFilter($album->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $album->exchangeArray($form->getData());
                $this->getServiceTicketTable()->saveAlbum($album);

                // Redirect to list of albums
                return $this->redirect()->toRoute('album');
            }
        }
        return array('form' => $form);
    }
    

 public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('album', array(
                'action' => 'add'
            ));
        }
        $album = $this->getServiceTicketTable()->getAlbum($id);

        $form  = new AlbumForm();
        $form->bind($album);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($album->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getServiceTicketTable()->saveAlbum($form->getData());

                // Redirect to list of albums
                return $this->redirect()->toRoute('album');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }

  public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('album');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getServiceTicketTable()->deleteAlbum($id);
            }

            // Redirect to list of albums
            return $this->redirect()->toRoute('album');
        }

        return array(
            'id'    => $id,
            'album' => $this->getServiceTicketTable()->getAlbum($id)
        );
    }
    
    public function getServiceTicketTable()
    {
    	if (!$this->serviceTicket) {
    		$sm = $this->getServiceLocator();
    		$this->serviceTicket = $sm->get('Kristins\Model\ServiceTicketTable');
    	}
    	return $this->serviceTicket;
    }
}