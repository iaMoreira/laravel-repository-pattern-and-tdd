<?php

namespace App\Traits;

use App\Exceptions\BaseException;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

trait ResponseTrait
{
    /**
     * Status code of response
     *
     * @var int
     */
    protected $statusCode = 200;

    /**
     * Message of response
     *
     * @var string
     */
    protected $message = '';

    /**
     * Status of response
     *
     * @var status
     */
    protected $status = 'success';

    /**
     * Getter for statusCode
     *
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Setter for statusCode
     *
     * @param int $statusCode Value to set
     *
     * @return self
     */
    public function setStatusCode(int $statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Setter for statusCode
     *
     * @param int $statusCode Value to set
     *
     * @return self
     */
    public function setMessage(string $message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Setter for status
     *
     * @param boolean $status Value to set
     *
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function respond(): JsonResponse
    {

        return response()->json([
            'status'    => $this->status,
            'code'      => $this->statusCode,
            'data'      => [],
            'message'   => $this->message
        ], $this->statusCode);
    }

    public function responseWithArray($data)
    {

        return response()->json([
            'status'    => $this->status,
            'code'      => $this->statusCode,
            'data'      => $data,
            'message'   => $this->message
        ], $this->statusCode, []);
    }

    protected function respondWithObject($item, $callback)
    {
        return response()->json([
            'status'    => $this->status,
            'code'      => $this->statusCode,
            'data'      => new $callback($item),
            'message'   => $this->message
        ], $this->statusCode, []);
    }

    /**
     * Return collection response from the application
     *
     * @param array|LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection $collection
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithCollection($collection, $callback, int $code = 200)
    {

        $callback::collection($collection);
        $resource = $collection->toArray();

        return response()->json([
            'status'        => 'success',
            'code'          => $code,
            'data'          => $resource['data'],
            'pagination'    => Arr::except($resource, 'data')
        ], $this->statusCode, []);
    }

    protected function respondWithNativeCollection($collection, $callback)
    {

        $new_item = $callback::collection($collection);
        $resource = $new_item->toArray($collection);

        return response()->json([
            'status' => 'success',
            'data' => $resource,
        ], $this->statusCode, []);
    }

    protected function responseValidation(array $validatorResponse)
    {
        return $this->setStatus('error')
            ->setStatusCode(400)
            ->setMessage('API.validation_error')
            ->responseWithArray($validatorResponse);
    }

    protected function responseEmpty()
    {
        return $this->setStatus('error')
            ->setStatusCode(400)
            ->setMessage('API.empty_request')
            ->respond();
    }

    protected function responseError(string $message, int $code = 500)
    {
        return $this->setStatus('error')
            ->setStatusCode($code)
            ->setMessage($message)
            ->respond();
    }

    protected function responseNotFound($data)
    {
        return $this->setStatus('error')
            ->setStatusCode(404)
            ->setMessage('API.not_found')
            ->responseWithArray($data);
    }

    protected function responseDeleted()
    {
        return $this->setStatus('success')
            ->setStatusCode(204)
            ->setMessage('API.deleted_successfully')
            ->respond();
    }

    protected function responseErrorException(Exception $ex)
    {
        $message = config('app.debug') ? $ex->getMessage() : 'API.unable_complete_operation';
        $code = 500;
        if ($ex instanceof BaseException) {
            $message = $ex->getMessage();
            $code = $ex->getCode();
        }
        return $this->setStatus('error')
            ->setStatusCode($code)
            ->setMessage($message)
            ->respond();
    }

    public function responseWithToken($data)
    {
        $token = $data['token'];
        unset($data['token']);
        $json = [
            'status' => 'success',
            'code' => 200,
            'data' => $data,
            'message' => null,
        ];

        return response()->json($json)->header('Authorization', 'Bearer ' . $token);
    }
}
