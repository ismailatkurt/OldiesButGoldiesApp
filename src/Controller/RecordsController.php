<?php

namespace App\Controller;

use App\Events\Record\Deleted;
use App\Events\Record\Saved;
use App\Events\Record\Updated;
use App\RequestFilters\Record\AllRequestFilter;
use App\RequestFilters\Record\CreateRequestFilter;
use App\RequestFilters\Record\DeleteRequestFilter;
use App\RequestFilters\Record\OneRequestFilter;
use App\RequestFilters\Record\UpdateRequestFilter;
use App\Services\Record\RecordService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Annotations as OA;

class RecordsController extends AbstractController
{
    /**
     * @var RecordService
     */
    private RecordService $recordService;

    /**
     * @var EventDispatcherInterface
     */
    private EventDispatcherInterface $eventDispatcher;

    /**
     * @param RecordService $recordService
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        RecordService $recordService,
        EventDispatcherInterface $eventDispatcher
    )
    {
        $this->recordService = $recordService;
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
     *     path="/records",
     *     tags={"records"},
     *     summary="Get all records",
     *     description="Returns all records available",
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
     *         description="List of records"
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

                $response = $this->recordService->all(
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
                'message' => 'Could not fetch records!'
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
     *     path="/records/{recordId}",
     *     tags={"records"},
     *     operationId="one",
     *     summary="Get single record",
     *     description="Returns the record with given ID.",
     *     @OA\Parameter(
     *         name="recordId",
     *         in="path",
     *         required=true,
     *         description="The id of the record to retrieve",
     *         @OA\Schema(
     *           type="integer",
     *           format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Returns Single Record"
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
                $response = $this->recordService->one($requestData['id']);
                $statusCode = 200;
            } else {
                $response = [
                    'error' => $requestFilter->getMessages()
                ];
            }
        } catch (Exception $exception) {
            $response = [
                'message' => 'Could not find record!'
            ];
        }

        return new JsonResponse($response, $statusCode);
    }

    /**
     * @param Request $request
     * @param CreateRequestFilter $requestFilter
     *
     * @return JsonResponse
     *
     * @OA\Post(
     *     path="/records",
     *     tags={"records"},
     *     operationId="create",
     *     summary="Create new record",
     *     description="Create new record with given data",
     *
     *     @OA\Response(
     *         response=200,
     *         description="Returns Created Record"
     *     ),
     *
     *     @OA\RequestBody(
     *         description="Input data format",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     description="Name of the record",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="artistId",
     *                     description="ID of related artist",
     *                     type="int",
     *                 ),
     *                 example={"name": "Down By The River", "artistId": 52}
     *             )
     *         )
     *     )
     * )
     */
    public function create(Request $request, CreateRequestFilter $requestFilter)
    {
        $statusCode = 400;

        try {
            $postData = json_decode($request->getContent(), true);
            $requestFilter->setData($postData);

            if ($requestFilter->isValid()) {
                $requestData = $requestFilter->getValues();
                $record = $this->recordService->create($requestData['name'], $requestData['artistId']);
                $this->eventDispatcher->dispatch(new Saved($record), Saved::NAME);

                $response = $record;
                $statusCode = 201;
            } else {
                $response = [
                    'error' => $requestFilter->getMessages()
                ];
            }
        } catch (UniqueConstraintViolationException $exception) {
            $response = [
                'message' => 'Record already exists.'
            ];
        } catch (Exception $exception) {
            $response = [
                'message' => 'Could not save record!'
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
     *     path="/records/{recordId}",
     *     tags={"records"},
     *     summary="Deletes a record",
     *     operationId="delete",
     *     @OA\Parameter(
     *         name="recordId",
     *         in="path",
     *         description="Id of the record to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error message",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Record not found",
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
                $record = $this->recordService->one($requestData['id']);
                if ($record) {
                    $this->recordService->delete($record);

                    $response = [
                        'Record deleted successfully!'
                    ];
                    $statusCode = 202;
                    $this->eventDispatcher->dispatch(new Deleted($record), Deleted::NAME);
                } else {
                    $response = [
                        'Record does not exist!'
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
                'Record could not be deleted!'
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
     *     path="/records/{recordId}",
     *     tags={"records"},
     *     operationId="update",
     *     summary="Update record",
     *     description="Update the record with given ID and provided post data.",
     *
     *     @OA\Response(
     *         response=200,
     *         description="Returns Updated Record"
     *     ),
     *
     *     @OA\RequestBody(
     *         description="Input data format",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     description="Updated name of the record",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="artistId",
     *                     description="ID of related artist",
     *                     type="int",
     *                 ),
     *                 example={"name": "Down By The River", "artistId": 52}
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
                $record = $this->recordService->update(
                    $requestData['id'],
                    $requestData['name'],
                    $requestData['artistId']
                );
                $response = $record;
                $statusCode = 204;
                $this->eventDispatcher->dispatch(new Updated($record), Updated::NAME);
            } else {
                $response = [
                    'error' => $requestFilter->getMessages()
                ];
            }
        } catch (UniqueConstraintViolationException $exception) {
            $response = [
                'message' => 'Record already exists.'
            ];
        } catch (Exception $exception) {
            $response = [
                'message' => 'Could not update record!'
            ];
        }
        return new JsonResponse($response, $statusCode);
    }
}
