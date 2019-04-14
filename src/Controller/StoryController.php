<?php

namespace App\Controller;

use App\Common\Update\UpdateStamp;
use App\Model\Story\Message\CreateStoryMessage;
use App\Model\Story\Message\ModifyStoryMessage;
use App\Model\Story\Message\RemoveStoryMessage;
use App\Model\Story\Query\FindStoryQuery;
use App\Model\Story\Query\ListStoriesQuery;
use Fig\Link\Link;
use FOS\RestBundle\Controller\ControllerTrait;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\ViewHandlerInterface;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Sulu\Component\Security\Authentication\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerTrait as SymfonyControllerTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class StoryController implements ClassResourceInterface
{
    use ControllerTrait;
    use SymfonyControllerTrait;

    /**
     * @var MessageBusInterface
     */
    protected $messageBus;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var string
     */
    private $defaultHub;

    /**
     * @var string
     */
    private $secretKey;

    public function __construct(
        MessageBusInterface $messageBus,
        TokenStorageInterface $tokenStorage,
        ViewHandlerInterface $viewHandler,
        string $defaultHub,
        string $secretKey
    ) {
        $this->messageBus = $messageBus;
        $this->tokenStorage = $tokenStorage;
        $this->defaultHub = $defaultHub;
        $this->secretKey = $secretKey;

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
        $envelope = $this->messageBus->dispatch($message);

        /** @var HandledStamp[] $handled */
        $handled = $envelope->all(HandledStamp::class);
        if ($handled) {
            return $this->handleView($this->view($handled[0]->getResult()));
        }

        $this->addLink($request, new Link('mercure', $this->defaultHub));
        $this->addLink($request, new Link('topic', 'http://sulu-mercure.localhost/stories/' . $message->getId()));

        $response = new JsonResponse(null, 202);

        /** @var UpdateStamp[] $handled */
        $update = $envelope->all(UpdateStamp::class);
        $response->headers->set('Update', $update[0]->getUpdateId());

        return $response;
    }

    public function getAction(string $id, Request $request): Response
    {
        $query = new FindStoryQuery($id);
        $this->messageBus->dispatch($query);

        $this->addLink($request, new Link('mercure', $this->defaultHub));
        $this->addLink($request, new Link('topic', 'http://sulu-mercure.localhost/stories/' . $id));

        $username = $this->getUser()->getUsername();
        $token = (new Builder())
            ->set('mercure', ['subscribe' => sprintf('http://sulu-mercure.localhost/user/%s', $username)])
            ->sign(new Sha256(), $this->secretKey)
            ->getToken();

        $response = $this->handleView($this->view($query->getResult()));
        $response->headers->set(
            'set-cookie',
            sprintf('mercureAuthorization=%s; path=/admin; httponly; SameSite=strict', $token)
        );

        return $response;
    }

    public function putAction(string $id, Request $request): Response
    {
        $message = new ModifyStoryMessage($id, $request->request->get('title'));
        $envelope = $this->messageBus->dispatch($message);

        /** @var HandledStamp[] $handled */
        $handled = $envelope->all(HandledStamp::class);
        if ($handled) {
            return $this->handleView($this->view($handled[0]->getResult()));
        }

        $this->addLink($request, new Link('mercure', $this->defaultHub));
        $this->addLink($request, new Link('topic', 'http://sulu-mercure.localhost/stories/' . $id));

        $response = new JsonResponse(null, 202);

        /** @var UpdateStamp[] $handled */
        $update = $envelope->all(UpdateStamp::class);
        $response->headers->set('Update', $update[0]->getUpdateId());

        return $response;
    }

    public function deleteAction(string $id, Request $request): Response
    {
        $envelope = $this->messageBus->dispatch(new RemoveStoryMessage($id));

        $this->addLink($request, new Link('mercure', $this->defaultHub));
        $this->addLink($request, new Link('topic', 'http://sulu-mercure.localhost/stories/' . $id));

        $response = new JsonResponse(null, 202);

        /** @var UpdateStamp[] $handled */
        $update = $envelope->all(UpdateStamp::class);
        $response->headers->set('Update', $update[0]->getUpdateId());

        return $response;
    }

    protected function getUser(): ?UserInterface
    {
        $token = $this->tokenStorage->getToken();
        if (null === $token) {
            return null;
        }

        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return null;
        }

        return $user;
    }
}
