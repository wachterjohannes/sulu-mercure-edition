<?php

namespace App\Common\Update;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UpdateMiddleware implements MiddlewareInterface
{
    /**
     * @var UpdateCollector
     */
    private $updateCollector;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var Publisher
     */
    private $publisher;

    public function __construct(
        UpdateCollector $updateCollector,
        Publisher $publisher,
        TokenStorageInterface $tokenStorage
    ) {
        $this->updateCollector = $updateCollector;
        $this->publisher = $publisher;
        $this->tokenStorage = $tokenStorage;
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        if (!$envelope->all(UpdateStamp::class)) {
            $user = $this->getUser();
            if ($user) {
                $username = $this->getUser()->getUsername();
                $envelope = $envelope->with(
                    new UpdateStamp(
                        Uuid::uuid4()->toString(),
                        [sprintf('http://sulu-mercure.localhost/user/%s', $username)]
                    )
                );
            }
        }

        $envelope = $stack->next()->handle($envelope, $stack);

        /** @var UpdateStamp[] $updateStamps */
        $updateStamps = $envelope->all(UpdateStamp::class);
        $targets = [];
        if ($updateStamps) {
            $targets = $updateStamps[0]->getTargets();
        }

        foreach ($this->updateCollector->release() as $update) {
            $targetedUpdate = new Update(
                $update->getTopics(),
                $update->getData(),
                $update->getTargets(), // array_merge($update->getTargets(), $targets) TODO authorization cookie for mercure
                $update->getId() ?? $updateStamps[0]->getUpdateId(),
                $update->getType(),
                $update->getRetry()
            );

            ($this->publisher)($targetedUpdate);
        }

        return $envelope;
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
