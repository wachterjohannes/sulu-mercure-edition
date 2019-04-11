<?php

namespace App\Model\Story\QueryHandler;

use App\Model\Story\Query\ListStoriesQuery;
use App\Model\Story\Story;
use Sulu\Component\Rest\ListBuilder\Doctrine\DoctrineListBuilder;
use Sulu\Component\Rest\ListBuilder\Doctrine\DoctrineListBuilderFactoryInterface;
use Sulu\Component\Rest\ListBuilder\FieldDescriptor;
use Sulu\Component\Rest\ListBuilder\ListRepresentation;
use Sulu\Component\Rest\ListBuilder\Metadata\FieldDescriptorFactoryInterface;
use Sulu\Component\Rest\RestHelperInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ListStoriesQueryHandler implements MessageHandlerInterface
{
    const ROUTE = 'app.get_stories';

    /**
     * @var DoctrineListBuilderFactoryInterface
     */
    private $listBuilderFactory;

    /**
     * @var RestHelperInterface
     */
    private $restHelper;

    /**
     * @var FieldDescriptorFactoryInterface
     */
    private $fieldDescriptorsFactory;

    public function __construct(
        DoctrineListBuilderFactoryInterface $listBuilderFactory,
        RestHelperInterface $restHelper,
        // fieldDescriptorsFactory is null because its only registered in the admin context.
        ?FieldDescriptorFactoryInterface $fieldDescriptorsFactory
    ) {
        $this->listBuilderFactory = $listBuilderFactory;
        $this->restHelper = $restHelper;

        if (!$fieldDescriptorsFactory) {
            throw new \RuntimeException(
                'FieldDescriptorFactory cannot be null - is it possible that you call this in the website context.'
            );
        }

        $this->fieldDescriptorsFactory = $fieldDescriptorsFactory;
    }

    public function __invoke(ListStoriesQuery $query): void
    {
        $stories = $this->createListRepresentation(
            Story::class,
            Story::LIST_KEY,
            Story::RESOURCE_KEY,
            $query->getQuery(),
            self::ROUTE
        );

        $query->setResult($stories);
    }

    protected function createListRepresentation(
        string $entityName,
        string $listKey,
        string $resourceKey,
        array $query,
        string $route,
        ?string $locale = null,
        array $attributes = []
    ): ListRepresentation {
        $fieldDescriptors = $this->getFieldDescriptors($listKey);

        /** @var DoctrineListBuilder $listBuilder */
        $listBuilder = $this->listBuilderFactory->create($entityName);
        if ($locale) {
            $listBuilder->setParameter('locale', $locale);
        }

        $this->restHelper->initializeListBuilder($listBuilder, $fieldDescriptors);
        $this->prepareListBuilder($listBuilder, $fieldDescriptors, $attributes);

        return new ListRepresentation(
            $this->getListResponse($listBuilder),
            $resourceKey,
            $route,
            $query,
            $listBuilder->getCurrentPage(),
            $listBuilder->getLimit(),
            $listBuilder->count()
        );
    }

    /**
     * @param FieldDescriptor[] $fieldDescriptors
     */
    protected function prepareListBuilder(
        DoctrineListBuilder $listBuilder,
        array $fieldDescriptors,
        array $attributes
    ): void {
        $listBuilder->setIdField($fieldDescriptors['id']);
    }

    /**
     * @return FieldDescriptor[]
     */
    protected function getFieldDescriptors(string $listKey): array
    {
        /** @var FieldDescriptor[] $fieldDescriptors */
        $fieldDescriptors = $this->fieldDescriptorsFactory->getFieldDescriptors($listKey);

        return $fieldDescriptors;
    }

    protected function getListResponse(DoctrineListBuilder $listBuilder): array
    {
        return $listBuilder->execute();
    }
}
