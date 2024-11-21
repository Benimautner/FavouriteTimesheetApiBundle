<?php

/*
 * This file is part of the "DemoBundle" for Kimai.
 * All rights reserved by Kevin Papst (www.kevinpapst.de).
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\FavouriteTimesheetApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\TagRepository;
use App\Repository\TimesheetRepository;
use Psr\EventDispatcher\EventDispatcherInterface;
use App\Timesheet\FavoriteRecordService;
use App\Timesheet\TimesheetService;

#[Route(path: '/timesheets/favourite')]
#[OA\Tag(name: 'Demo')]
#[IsGranted('API')]
final class FavouriteTimesheetApiController extends AbstractController
{
    public function __construct(
        private readonly ViewHandlerInterface $viewHandler,
        private readonly TimesheetRepository $repository,
        private readonly TagRepository $tagRepository,
        private readonly EventDispatcherInterface $dispatcher,
        private readonly TimesheetService $service,
        private readonly FavoriteRecordService $favoriteRecordService
    ) {
    }
    /**
     * Returns a collection of demo entities
     */
    #[IsGranted('view_own_timesheet')]
    #[OA\Response(response: 200, description: 'Returns the collection of active timesheet records for the current user', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/TimesheetCollectionExpanded')))]
    #[Route(methods: ['GET'])]
    public function cgetAction(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $data = $this->favoriteRecordService->favoriteEntries($user);

        $view = new View($data, 200);
        //$view->getContext()->setGroups(self::GROUPS_COLLECTION_FULL);

        return $this->viewHandler->handle($view);
    }

}