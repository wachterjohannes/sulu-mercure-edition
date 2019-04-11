<?php

namespace App\Controller;

use App\Model\Story\Message\CreateStoryMessage;
use App\Model\Story\Message\ModifyStoryMessage;
use App\Model\Story\Message\RemoveStoryMessage;
use App\Model\Story\Query\FindStoryQuery;
use App\Model\Story\Query\ListStoriesQuery;
use Fig\Link\Link;
use FOS\RestBundle\Controller\ControllerTrait;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\ViewHandlerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerTrait as SymfonyControllerTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

class StoryController implements ClassResourceInterface
{
    use ControllerTrait;
    use SymfonyControllerTrait;

    /**
     * @var MessageBusInterface
     */
    protected $messageBus;

    /**
     * @var string
     */
    private $defaultHub;

    public function __construct(MessageBusInterface $messageBus, ViewHandlerInterface $viewHandler, string $defaultHub)
    {
        $this->messageBus = $messageBus;
        $this->defaultHub = $defaultHub;

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

        $this->addLink($request, new Link('mercure', $this->defaultHub));
        $this->addLink($request, new Link('topic', 'http://sulu-mercure.localhost/stories/' . $message->getId()));

        return new JsonResponse(null, 202);
    }

    public function getAction(string $id, Request $request): Response
    {
        $query = new FindStoryQuery($id);
        $this->messageBus->dispatch($query);

        $this->addLink($request, new Link('mercure', $this->defaultHub));
        $this->addLink($request, new Link('topic', 'http://sulu-mercure.localhost/stories/' . $id));

        return $this->handleView($this->view($query->getResult()));
    }

    public function putAction(string $id, Request $request): Response
    {
        $message = new ModifyStoryMessage($id, $request->request->get('title'));
        $this->messageBus->dispatch($message);

        $this->addLink($request, new Link('mercure', $this->defaultHub));
        $this->addLink($request, new Link('topic', 'http://sulu-mercure.localhost/stories/' . $id));

        return new JsonResponse(null, 202);
    }

    public function deleteAction(string $id, Request $request): Response
    {
        $this->messageBus->dispatch(new RemoveStoryMessage($id));

        $this->addLink($request, new Link('mercure', $this->defaultHub));
        $this->addLink($request, new Link('topic', 'http://sulu-mercure.localhost/stories/' . $id));

        return new JsonResponse(null, 202);
    }
}
