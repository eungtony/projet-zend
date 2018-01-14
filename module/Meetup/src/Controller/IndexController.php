<?php

declare(strict_types=1);

namespace Meetup\Controller;

use Meetup\Form\MeetupForm;
use Meetup\Repository\MeetupRepository;
use Zend\Http\PhpEnvironment\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

final class IndexController extends AbstractActionController
{
    /**
     * @var MeetupRepository
     */
    private $meetupRepository;

    /**
     * @var MeetupForm
     */
    private $meetupForm;

    public function __construct(MeetupRepository $meetupRepository, MeetupForm $meetupForm)
    {
        $this->meetupRepository = $meetupRepository;
        $this->meetupForm = $meetupForm;
    }

    public function indexAction()
    {
        return new ViewModel([
            'meetups' => $this->meetupRepository->findAll(),
        ]);
    }

    public function addAction()
    {
        $form = $this->meetupForm;

        /* @var $request Request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $meetup = $this->meetupRepository->createMeetup(
                    $form->getData()['title'],
                    $form->getData()['description'],
                    $form->getData()['beginningDate'],
                    $form->getData()['endDate']
                );
                $this->meetupRepository->persist($meetup);

                return $this->redirect()->toRoute('meetups');
            }
        }

        $form->prepare();

        return new ViewModel([
            'form' => $form,
        ]);
    }

    public function editAction()
    {
        $request = $this->getRequest();
        $meetup  = $this->meetupRepository->find($this->params('id'));
        $form = $this->meetupForm->bind($meetup);

        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $meetup = $form->getData();
                $this->meetupRepository->persist($meetup);

                return $this->redirect()->toRoute('meetups');
            }
        }

        return new ViewModel(array(
            'form' => $form
        ));
    }

    public function deleteAction()
    {
        $meetup  = $this->meetupRepository->find($this->params('id'));
        $this->meetupRepository->remove($meetup);

        return $this->redirect()->toRoute('meetups');
    }

    public function meetupAction()
    {
        $meetup  = $this->meetupRepository->find($this->params('id'));

        return new ViewModel(array(
            'meetup' => $meetup
        ));
    }
}
