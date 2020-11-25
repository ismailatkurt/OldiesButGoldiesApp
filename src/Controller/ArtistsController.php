<?php

namespace App\Controller;

use App\Events\Artist\Saved;
use App\Events\Artist\Updated;
use App\Events\Record\Deleted;
use App\RequestFilters\Artist\AllRequestFilter;
use App\RequestFilters\Artist\CreateRequestFilter;
use App\RequestFilters\Artist\OneRequestFilter;
use App\RequestFilters\Artist\UpdateRequestFilter;
use App\RequestFilters\Record\DeleteRequestFilter;
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
     *
     * @OA\Get(
     *     operationId="index",
     *     path="/artists",
     *     tags={"artists"},
     *     summary="Get all artists",
     *     description="Returns all artists available",
     *     @OA\Parameter(name="page",
     *       in="query",
     *       required=false,
     *       @OA\Schema(type="int")
     *     ),
     *     @OA\Parameter(name="limit",
     *       in="query",
     *       required=false,
     *       @OA\Schema(type="int")
     *     ),
     *     @OA\Parameter(name="searchTerm",
     *       in="query",
     *       required=false,
     *       @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of artists"
     *     )
     * )
     */
    public function all(Request $request, AllRequestFilter $allRequestFilter)
    {
        try {
            $queryParameters = $this->getQueryParameters($request->getQueryString());

            $allRequestFilter->setData($queryParameters);
            if ($allRequestFilter->isValid()) {
                $requestData = $allRequestFilter->getValues();

                $response = $this->artistService->all(
                    $requestData['page'],
                    $requestData['limit'],
                    $requestData['searchTerm']
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
     * @param int $id
     * @param OneRequestFilter $requestFilter
     *
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/artists/{artistId}",
     *     tags={"artists"},
     *     operationId="one",
     *     summary="Get single artist",
     *     description="Returns the artist with given ID.",
     *     @OA\Parameter(
     *         name="artistId",
     *         in="path",
     *         required=true,
     *         description="The id of the artist to retrieve",
     *         @OA\Schema(
     *           type="integer",
     *           format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Returns Single artist"
     *     )
     * )
     */
    public function one(int $id, OneRequestFilter $requestFilter)
    {
        $statusCode = 404;

        try {
            $requestFilter->setData(['id' => $id]);
            if ($requestFilter->isValid()) {
                $requestData = $requestFilter->getValues();
                $response = $this->artistService->one($requestData['id']);
                $statusCode = 200;
            } else {
                $response = [
                    'error' => $requestFilter->getMessages()
                ];
            }
        } catch (Exception $exception) {
            $response = [
                'message' => 'Could not find artist!'
            ];
        }

        return new JsonResponse($response, $statusCode);
    }

    /**
     * @param Request $request
     * @param CreateRequestFilter $createRequestFilter
     *
     * @return JsonResponse
     *
     * @OA\Post(
     *     path="/artists",
     *     tags={"artists"},
     *     operationId="create",
     *     summary="Create new artist",
     *     description="Create new artist with given data",
     *
     *     @OA\Response(
     *         response=200,
     *         description="Returns Created Artist"
     *     ),
     *
     *     @OA\RequestBody(
     *         description="Input data format",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     description="Name of the artist",
     *                     type="string",
     *                 ),
     *                 example={"name": "Edith Piaf"}
     *             )
     *         )
     *     )
     * )
     */
    public function create(Request $request, CreateRequestFilter $createRequestFilter)
    {
        $statusCode = 400;

        try {
            $postData = json_decode($request->getContent(), true);
            $createRequestFilter->setData($postData);

            if ($createRequestFilter->isValid()) {
                $requestData = $createRequestFilter->getValues();
                $artist = $this->artistService->create($requestData['name']);
                $this->eventDispatcher->dispatch(new Saved($artist), Saved::NAME);

                $response = $artist;
                $statusCode = 201;
            } else {
                $response = [
                    'error' => $createRequestFilter->getMessages()
                ];
            }
        } catch (UniqueConstraintViolationException $exception) {
            $statusCode = 409;
            $response = [
                'message' => 'Artist already exists.'
            ];
        } catch (Exception $exception) {
            $response = [
                'message' => 'Could not saved artist!'
            ];
        }

        return new JsonResponse($response, $statusCode);
    }

    /**
     * @param int $id
     * @param Request $request
     * @param UpdateRequestFilter $requestFilter
     *
     * @return JsonResponse
     *
     * @OA\Put(
     *     path="/artists/{artistId}",
     *     tags={"artists"},
     *     operationId="update",
     *     summary="Update artist",
     *     description="Update the artist with given ID and provided post data.",
     *
     *     @OA\Parameter(
     *         name="artistId",
     *         in="path",
     *         required=true,
     *         description="The id of the artist to retrieve",
     *         @OA\Schema(
     *           type="integer",
     *           format="int64"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=204,
     *         description="No content"
     *     ),
     *
     *     @OA\RequestBody(
     *         description="Input data format",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     description="Updated name of the artist",
     *                     type="string",
     *                 ),
     *                 example={"name": "Daft Punk"}
     *             )
     *         )
     *     )
     * )
     */
    public function update(int $id, Request $request, UpdateRequestFilter $requestFilter)
    {
        $statusCode = 400;

        try {
            $postData = json_decode($request->getContent(), true);
            $postData['id'] = $id;

            $requestFilter->setData($postData);

            if ($requestFilter->isValid()) {
                $requestData = $requestFilter->getValues();
                $artist = $this->artistService->update(
                    $requestData['id'],
                    $requestData['name']
                );
                $statusCode = 204;
                $this->eventDispatcher->dispatch(new Updated($artist), Updated::NAME);
            } else {
                $response = [
                    'error' => $requestFilter->getMessages()
                ];
            }
        } catch (UniqueConstraintViolationException $exception) {
            $response = [
                'message' => 'Artist already exists.'
            ];
        } catch (Exception $exception) {
            $response = [
                'message' => 'Could not update artist!'
            ];
        }

        return new JsonResponse($response, $statusCode);
    }

    /**
     * @param int $id
     * @param DeleteRequestFilter $requestFilter
     *
     * @return JsonResponse
     *
     * @OA\Delete(
     *     path="/artists/{recordId}",
     *     tags={"artists"},
     *     summary="Deletes a artist",
     *     operationId="delete",
     *     @OA\Parameter(
     *         name="artistId",
     *         in="path",
     *         description="Id of the artist to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=202,
     *         description="Deleted successfully",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error message",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Artist not found",
     *     )
     * )
     */
    public function delete(int $id, DeleteRequestFilter $requestFilter)
    {
        $statusCode = 400;

        try {
            $requestFilter->setData(['id' => $id]);
            if ($requestFilter->isValid()) {
                $requestData = $requestFilter->getValues();
                $artist = $this->artistService->one($requestData['id']);
                if ($artist) {
                    $this->artistService->delete($artist);

                    $response = [
                        'Record deleted successfully!'
                    ];
                    $statusCode = 202;
                    $this->eventDispatcher->dispatch(new Deleted($artist), Deleted::NAME);
                } else {
                    $response = [
                        'Artist does not exist!'
                    ];
                }
            } else {
                $response = [
                    'error' => $requestFilter->getMessages()
                ];
            }
        } catch (\Exception $exception) {
            $statusCode = 404;
            $response = [
                'Artist could not be deleted! An error occurred!'
            ];
        }

        return new JsonResponse($response, $statusCode);
    }
}
