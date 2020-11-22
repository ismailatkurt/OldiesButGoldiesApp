<?php

namespace App\Controller;

use App\Events\Record\Deleted;
use App\Events\Record\Saved;
use App\RequestFilters\Record\AllRequestFilter;
use App\RequestFilters\Record\CreateRequestFilter;
use App\Services\Record\RecordService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Exception;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
    ) {
        $this->recordService = $recordService;
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

                $response = $this->recordService->all(
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
                'message' => 'Could not fetch records!'
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
        $statusCode = 400;

        try {
            $postData = json_decode($request->getContent(), true);
            $createRequestFilter->setData($postData);

            if ($createRequestFilter->isValid()) {
                $requestData = $createRequestFilter->getValues();
                $record = $this->recordService->create($requestData['name'], $requestData['artistId']);
                $this->eventDispatcher->dispatch(new Saved($record), Saved::NAME);

                $response = $record;
                $statusCode = 201;
            } else {
                $response = [
                    'error' => $createRequestFilter->getMessages()
                ];
            }
        } catch (UniqueConstraintViolationException $exception) {
            $response = [
                'message' => 'Record already exists.'
            ];
        } catch (Exception $exception) {
            $response = [
                'message' => 'Could not saved record!'
            ];
        }

        return new JsonResponse($response, $statusCode);
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function delete(int $id)
    {
        $statusCode = 400;

        try {
            $record = $this->recordService->one($id);
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
        } catch (ORMInvalidArgumentException $exception) {
            $response = [
                'Invalid argument!'
            ];
        } catch (\Exception $exception) {
            $response = [
                'Record could not be deleted!'
            ];
        }

        return new JsonResponse($response, $statusCode);
    }
}
