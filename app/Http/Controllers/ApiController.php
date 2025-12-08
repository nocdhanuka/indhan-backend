<?php

namespace App\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Http\Request;
use App\Http\Requests;
use Response;
use \Illuminate\Http\Response as Res;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ApiController
 * @package App\Modules\Api\Lesson\Controllers
 */
class ApiController extends Controller {

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->beforeFilter('auth', ['on' => 'post']);
    }

    /**
     * @var int
     */
    protected $statusCode = Res::HTTP_OK;

    /**
     * @return mixed
     */
    public function getStatusCode() {
        return $this->statusCode;
    }

    /**
     * @param $message
     * @return json response
     */
    public function setStatusCode($statusCode) {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function respondCreated($message, $data = null) {
        return $this->respond([
            'status' => 'success',
            'status_code' => Res::HTTP_CREATED,
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * @param Paginator $paginate
     * @param $data
     * @return mixed
     */
    protected function respondWithPagination(Paginator $paginate, $data, $message) {
        $data = array_merge($data, [
            'paginator' => [
                'total_count' => $paginate->total(),
                'total_pages' => ceil($paginate->total() / $paginate->perPage()),
                'current_page' => $paginate->currentPage(),
                'limit' => $paginate->perPage(),
            ]
        ]);
        DB::disconnect(env('DB_DATABASE'));
        // Log::info(env('DB_DATABASE')." Connection Closed-1");
        return $this->respond([
            'status' => 'success',
            'status_code' => Res::HTTP_OK,
            'message' => $message,
            'data' => $data
        ]);
    }

    public function respondNotFound($message = 'Not Found!') {
        $errorMessage = array('Invalid Attempt!','Invalid Attempt !!','Token Expired');
        if(in_array($message, $errorMessage)) {
            $statusCode = '409';
        } else {
            $statusCode = Res::HTTP_NOT_FOUND;
        }
        DB::disconnect(env('DB_DATABASE'));
        // Log::info(env('DB_DATABASE')." Connection Closed-2");
        return $this->respond([
            'status' => 'error',
            'status_code' => $statusCode,
            'message' => $message,
        ]);
    }

    public function respondInternalError($message) {
        $errorMessage = array('Invalid Attempt!','Invalid Attempt !!','Token Expired');
        if(in_array($message, $errorMessage)) {
            $statusCode = '409';
        } else {
            $statusCode = Res::HTTP_INTERNAL_SERVER_ERROR;
        }
        DB::disconnect(env('DB_DATABASE'));
        // Log::info(env('DB_DATABASE')." Connection Closed-3");
        return $this->respond([
            'status' => 'error',
            'status_code' => $statusCode,
            'message' => $message,
        ]);
    }

    public function respondValidationError($message, $errors) {
        $errorMessage = array('Invalid Attempt!','Invalid Attempt !!','Token Expired');
        if(in_array($message, $errorMessage)) {
            $statusCode = '409';
        } else {
            $statusCode = Res::HTTP_UNPROCESSABLE_ENTITY;
        }
        DB::disconnect(env('DB_DATABASE'));
        // Log::info(env('DB_DATABASE')." Connection Closed-4");
        return $this->respond([
            'status' => 'error',
            'status_code' => $statusCode,
            'message' => $message,
            'data' => $errors
        ]);
    }

    public function respond($data, $headers = []) {
        DB::disconnect(env('DB_DATABASE'));
        // Log::info(env('DB_DATABASE')." Connection Closed-5");
        return Response::json($data, $this->getStatusCode(), $headers);
    }

    public function respondWithError($message) {
        $errorMessage = array('Invalid Attempt!','Invalid Attempt !!','Token Expired');
        if(in_array($message, $errorMessage)) {
            $statusCode = '409';
        } else {
            $statusCode = Res::HTTP_UNAUTHORIZED;
        }
        DB::disconnect(env('DB_DATABASE'));
        // Log::info(env('DB_DATABASE')." Connection Closed-6");
        return $this->respond([
            'status' => 'error',
            'status_code' => $statusCode,
            'message' => $message,
        ]);
    }

    public function respondWithConflict($message) {
        $errorMessage = array('Invalid Attempt!','Invalid Attempt !!','Token Expired');
        if(in_array($message, $errorMessage)) {
            $statusCode = '409';
        } else {
            $statusCode = Res::HTTP_CONFLICT;
        }
        DB::disconnect(env('DB_DATABASE'));
        // Log::info(env('DB_DATABASE')." Connection Closed-7");
        return $this->respond([
            'status' => 'error',
            'status_code' => $statusCode,
            'message' => $message,
        ]);
    }
}
