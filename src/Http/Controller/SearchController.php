<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Application\Search\SearchQuery;
use App\Application\Search\SearchServiceLocator;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends BaseController
{
    #[Route('/search/{subject}', name: 'search')]
    public function __invoke(Request $request, string $subject, SearchServiceLocator $serviceLocator): JsonResponse
    {
        try {
            $query = SearchQuery::fromRequest($request);
            $searchProvider = $serviceLocator->getSearchProvider($subject);
        } catch (InvalidArgumentException) {
            return $this->json([], Response::HTTP_BAD_REQUEST);
        }

        return $this->json($searchProvider->search($query));
    }
}
