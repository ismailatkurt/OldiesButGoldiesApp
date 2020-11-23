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
     *     summary="Returns most accurate search result object",
     *     description="Search for an object, if found return it!",
     *     @OA\Response(
     *         response=200,
     *         description="List of records"
     *     )
     * )
     *
     * @OA\Link(link="UserRepositories",
     *   operationId="getRepositoriesByOwner",
     *   parameters={"username"="$response.body#/username"}
     * )
     */
    public function index(Request $request, AllRequestFilter $allRequestFilter)
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
     *     summary="Returns most accurate search result object",
     *     description="Search for an object, if found return it!",
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
            $response = [
                'Record could not be deleted!'
            ];
        }

        return new JsonResponse($response, $statusCode);
    }

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
