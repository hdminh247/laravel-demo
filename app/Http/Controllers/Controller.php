<?php

namespace App\Http\Controllers;
use Validator;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * HTTP header status code.
     *
     * @var int
     */
    protected $statusCode = 200;

    /**
     * Illuminate\Http\Request instance.
     *
     * @var Request
     */
    protected $request;

    /**
     * @var Validator
     */
    public $validator;

    /**
     * @var $auth
     */
    public $auth;

    /**
     * @var $apiErrorCodes
     */
    public $apiErrorCodes;

    /**
     * @var $apiSuccessMessage
     */
    public $apiSuccessMessage;

    /**
     * @var $email
     */
    public $emailMessage;

    /**
     * @var $phoneMessage
     */
    public $phoneMessage;

    /**
     * @var $notificationMessage
     */
    public $notificationMessage;

    /**
     * @var $ip
     */
    public $ip;


    public function __construct(Request $request)
    {
        $this->request = $request;

    }


    /**
     * Getter for statusCode.
     *
     * @return int
     */
    protected function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Setter for statusCode.
     *
     * @param int $statusCode Value to set
     *
     * @return self
     */
    protected function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * @param  string $errorMessage
     * @param  int $errorCode
     * @param  null $statusCode
     * @param  array $headers
     * @return mixed
     */
    protected function respondWithErrorMessage($errorMessage, $errorCode = null, $statusCode = null, array $headers = [])
    {
        // if status code not change to error status => set it 400 error
        if (is_null($statusCode)) {
            $this->setStatusCode(400);
        } else {
            $this->setStatusCode($statusCode);
        }

        $response = array(
            'error' => true,
            'data' => null,
            'errors' => (object) array("messaage" => $errorMessage, "code" => $errorCode)
        );
        return response()->json($response, $this->statusCode, $headers);
    }

    /**
     * @param  string $errorMessage
     * @param  int $errorCode
     * @param  null $statusCode
     * @param  array $headers
     * @return mixed
     */
    protected function respondErrorWithCode($errorCode = null, $statusCode = null, array $headers = [])
    {
        // if status code not change to error status => set it 400 error
        if (is_null($statusCode)) {
            $this->setStatusCode(400);
        } else {
            $this->setStatusCode($statusCode);
        }

        $response = array(
            'error' => true,
            'data' => null,
            'errors' => (object) array("Message" => $this->apiErrorCodes[$this->apiErrorCodes['ApiErrorCodesFlip'][$errorCode]], "Code" => $errorCode)
        );
        return response()->json($response, $this->statusCode, $headers);
    }


    /**
     * @param  \App\Common\ErrorFormat[] $errors
     * @param  null $statusCode
     * @param  array $headers
     * @return mixed
     */
    protected function respondWithError($errors, $statusCode = null, array $headers = [])
    {
        // if status code not change to error status => set it 400 error
        if (is_null($statusCode)) {
            $this->setStatusCode(400);
        } else {
            $this->setStatusCode($statusCode);
        }
        $parseErrors = array();
        foreach ($errors as $error) {
            $parseErrors[] = new ErrorFormat($error[0], intval($error[1]));
        }

        $response = array(
            'error' => true,
            'data' => null,
            'errors' => (object) array("messaage" => $error[0], "code" => intval($error[1]))
        );
        return response()->json($response, $this->statusCode, $headers);
    }

    /**
     * @param    {array|object|string} $data
     * @param    array $headers
     * @return                         mixed
     */
    protected function respondWithSuccess($data, $statusCode = null, array $headers = [])
    {
        // if status code not change to error status => set it 400 error
        if (is_null($statusCode)) {
            $this->setStatusCode(200);
        } else {
            $this->setStatusCode($statusCode);
        }

        $response = array(
            'error' => false,
            'data' => $data,
            'errors' => null
        );

        return response()->json($response, $this->statusCode, $headers);
    }


    /**
     * Generate a Response with a 403 HTTP header and a given message.
     *
     * @param   string $message
     * @param   int $errorCode
     * @param    array $headers
     * @return  mixed
     */
    protected function errorForbidden($message = 'Forbidden', $errorCode = 0, array $headers = [])
    {
        return $this->respondWithErrorMessage($message, $errorCode, 403, $headers);
    }

    /**
     * Generate a Response with a 500 HTTP header and a given message.
     *
     * @param   string $message
     * @param   int $errorCode
     * @param    array $headers
     *
     * @return Response
     */
    protected function errorInternalError($message = 'Internal Error', $errorCode = 0, array $headers = [])
    {
        return $this->respondWithErrorMessage($message, $errorCode, 500, $headers);
    }

    /**
     * Generate a Response with a 404 HTTP header and a given message.
     *
     * @param   string $message
     * @param   int $errorCode
     * @param    array $headers
     *
     * @return Response
     */
    protected function errorNotFound($message = 'Resource Not Found', $errorCode = 0, array $headers = [])
    {
        return $this->respondWithErrorMessage($message, $errorCode, 404, $headers);
    }

    /**
     * Generate a Response with a 401 HTTP header and a given message.
     *
     * @param   string $message
     * @param   int $errorCode
     * @param    array $headers
     *
     * @return Response
     */
    protected function errorUnauthorized($message = 'Unauthorized', $errorCode = 0, array $headers = [])
    {
        return $this->respondWithErrorMessage($message, $errorCode, 401, $headers);
    }

    /**
     * Generate a Response with a 400 HTTP header and a given message.
     *
     * @param   string $message
     * @param   int $errorCode
     * @param    array $headers
     *
     * @return Response
     */
    protected function errorWrongArgs($message = 'Wrong Arguments', $errorCode = 0, array $headers = [])
    {
        return $this->respondWithErrorMessage($message, $errorCode, 400, $headers);
    }

    /**
     * Generate a Response with a 501 HTTP header and a given message.
     *
     * @param   string $message
     * @param   int $errorCode
     * @param    array $headers
     *
     * @return Response
     */
    protected function errorNotImplemented($message = 'Not implemented', $errorCode = 0, array $headers = [])
    {
        return $this->respondWithErrorMessage($message, $errorCode, 501, $headers);
    }


    /**
     * Generate a Response with a 400 HTTP header and a given message.
     *
     * @param   Validation $validator
     *
     * @return Response
     */
    protected function errorWithValidation($validator)
    {
        $errors = $validator->errors();
        $arr = array();
        foreach ($errors->all() as $code){
            array_push($arr,[
                'Code: '=> $code,
                'Message: ' => $this->apiErrorCodes[$this->apiErrorCodes['ApiErrorCodesFlip'][$code]]
            ]);
        }

        $response = array(
            'error' => true,
            'data' => null,
            'errors' =>$arr
        );
        return response()->json($response, 400, array());
    }

}
