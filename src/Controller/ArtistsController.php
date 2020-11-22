<?php

namespace App\Controller;

use App\Events\Artist\Saved;
use App\RequestFilters\Artist\AllRequestFilter;
use App\RequestFilters\Artist\CreateRequestFilter;
use App\Services\Artist\ArtistService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ArtistsController extends AbstractController
{
    /**
     * @var ArtistService
     */
    private ArtistService $artistService;

    /**
     * @var EventDispatcherInterface
     */
    private EventDispatcherInterface $eventDispatcher;

    /**
     * @param ArtistService $artistService
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        ArtistService $artistService,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->artistService = $artistService;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param Request $request
     * @param AllRequestFilter $allRequestFilter
     *
     * @return JsonResponse
     */
    public function index(Request $request, AllRequestFilter $allRequestFilter)
    {
        try {
            $queryParameters = $this->getQueryParameters($request->getQueryString());

            $allRequestFilter->setData($queryParameters);
            if ($allRequestFilter->isValid()) {
                $requestData = $allRequestFilter->getValues();

                $response = $this->artistService->all(
                    $requestData['page'],
                    $requestData['limit'],
                    $requestData['search-term']
                );
            } else {
                $response = [
                    'error' => $allRequestFilter->getMessages()
                ];
            }
        } catch (InvalidArgumentException $e) {
            $response = [
                'message' => 'Could not fetch artists!'
            ];
        }

        return new JsonResponse($response);
    }

    /**
     * @param Request $request
     * @param CreateRequestFilter $createRequestFilter
     *
     * @return JsonResponse
     */
    public function create(Request $request, CreateRequestFilter $createRequestFilter)
    {
        try {
            $postData = json_decode($request->getContent(), true);
            $createRequestFilter->setData($postData);

            if ($createRequestFilter->isValid()) {
                $requestData = $createRequestFilter->getValues();
                $artist = $this->artistService->create($requestData['name']);
                $this->eventDispatcher->dispatch(new Saved($artist), Saved::NAME);

                $response = $artist;
            } else {
                $response = [
                    'error' => $createRequestFilter->getMessages()
                ];
            }
        } catch (UniqueConstraintViolationException $exception) {
            $response = [
                'message' => 'Artist already exists.'
            ];
        } catch (Exception $exception) {
            var_dump($exception->getMessage()); die;
            $response = [
                'message' => 'Could not saved artist!'
            ];
        }

        return new JsonResponse($response);
    }
}
