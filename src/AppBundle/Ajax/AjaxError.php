<?php

namespace AppBundle\Ajax;

use Symfony\Component\HttpFoundation\JsonResponse;

class AjaxError extends JsonResponse
{
    /**
     * @param string $message The error message
     * @param int    $status  The response http status
     * @param array  $headers An array of response headers
     * @param bool   $json    If the data is already a JSON string
     */
    public function __construct($message = null, $status = 200, $headers = array(), $json = false)
    {
        $data = [
            'status' => false,
            'message' => $message,
        ];

        parent::__construct($data, $status, $headers, $json);
    }
}
