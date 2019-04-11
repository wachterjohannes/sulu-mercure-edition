<?php

namespace App\Admin;

use App\Model\Story\Story;
use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\Routing\RouteBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Navigation\Navigation;
use Sulu\Bundle\AdminBundle\Navigation\NavigationItem;

class StoryAdmin extends Admin
{
    const LIST_ROUTE = 'app_stories.list';
    const ADD_FORM_ROUTE = 'app_stories.add';
    const EDIT_FORM_ROUTE = 'app_stories.edit';

    /**
     * @var RouteBuilderFactoryInterface
     */
    private $routeBuilderFactory;

    public function __construct(RouteBuilderFactoryInterface $routeBuilderFactory)
    {
        $this->routeBuilderFactory = $routeBuilderFactory;
    }

    public function getNavigation(): Navigation
    {
        $rootNavigationItem = $this->getNavigationItemRoot();

        $module = $this->getNavigationItemSettings();

        $stories = new NavigationItem('app.stories');
        $stories->setPosition(5);
        $stories->setMainRoute(self::LIST_ROUTE);
        $module->addChild($stories);
        $rootNavigationItem->addChild($module);

        return new Navigation($rootNavigationItem);
    }

    public function getRoutes(): array
    {
        $formToolbarActions = [
            'sulu_admin.save',
            'sulu_admin.delete',
        ];

        $listToolbarActions = [
            'sulu_admin.add',
            'sulu_admin.delete',
        ];

        return [
            $this->routeBuilderFactory->createListRouteBuilder(self::LIST_ROUTE, '/stories')
                ->setResourceKey(Story::RESOURCE_KEY)
                ->setListKey(Story::LIST_KEY)
                ->setTitle('app.stories')
                ->addListAdapters(['table'])
                ->setAddRoute(static::ADD_FORM_ROUTE)
                ->setEditRoute(static::EDIT_FORM_ROUTE)
                ->addToolbarActions($listToolbarActions)
                ->getRoute(),
            $this->routeBuilderFactory->createResourceTabRouteBuilder(static::ADD_FORM_ROUTE, '/stories/add')
               ->setResourceKey(Story::RESOURCE_KEY)
                ->setBackRoute(static::LIST_ROUTE)
                ->getRoute(),
            $this->routeBuilderFactory->createFormRouteBuilder(static::ADD_FORM_ROUTE . '.details', '/details')
               ->setResourceKey(Story::RESOURCE_KEY)
                ->setFormKey(Story::FORM_KEY)
                ->setTabTitle('sulu_admin.details')
                ->setEditRoute(static::EDIT_FORM_ROUTE)
                ->addToolbarActions($formToolbarActions)
                ->setParent(static::ADD_FORM_ROUTE)
                ->getRoute(),
            $this->routeBuilderFactory->createResourceTabRouteBuilder(static::EDIT_FORM_ROUTE, '/stories/:id')
               ->setResourceKey(Story::RESOURCE_KEY)
                ->setBackRoute(static::LIST_ROUTE)
                ->setTitleProperty('fullName')
                ->getRoute(),
            $this->routeBuilderFactory->createFormRouteBuilder(static::EDIT_FORM_ROUTE . '.details', '/details')
               ->setResourceKey(Story::RESOURCE_KEY)
                ->setFormKey(Story::FORM_KEY)
                ->setTabTitle('sulu_admin.details')
                ->addToolbarActions($formToolbarActions)
                ->setParent(static::EDIT_FORM_ROUTE)
                ->getRoute(),
        ];
    }
}
