<?php

namespace App\Controller;

use App\Model\Story\Message\CreateStoryMessage;
use App\Model\Story\Message\ModifyStoryMessage;
use App\Model\Story\Message\RemoveStoryMessage;
use App\Model\Story\Query\FindStoryQuery;
use App\Model\Story\Query\ListStoriesQuery;
use FOS\RestBundle\Controller\ControllerTrait;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\ViewHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

class StoryController implements ClassResourceInterface
{
    use ControllerTrait;

    /**
     * @var MessageBusInterface
     */
    protected $messageBus;

    public function __construct(MessageBusInterface $messageBus, ViewHandlerInterface $viewHandler)
    {
        $this->messageBus = $messageBus;

        $this->setViewHandler($viewHandler);
    }

    public function cgetAction(Request $request): Response
    {
        $query = new ListStoriesQuery($request->query->all());
        $this->messageBus->dispatch($query);

        return $this->handleView($this->view($query->getResult()));
    }

    public function postAction(Request $request): Response
    {
        $message = new CreateStoryMessage($request->request->get('title'));
        $this->messageBus->dispatch($message);

        return $this->getAction($message->getId());
    }

    public function getAction(string $id): Response
    {
        $query = new FindStoryQuery($id);
        $this->messageBus->dispatch($query);

        return $this->handleView($this->view($query->getResult()));
    }

    public function putAction(string $id, Request $request): Response
    {
        $message = new ModifyStoryMessage($id, $request->request->get('title'));
        $this->messageBus->dispatch($message);

        return $this->getAction($message->getId());
    }

    public function deleteAction(string $id): Response
    {
        $this->messageBus->dispatch(new RemoveStoryMessage($id));

        return $this->handleView($this->view());
    }
}
