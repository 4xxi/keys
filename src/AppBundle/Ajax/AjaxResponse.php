<?php

namespace AppBundle\Ajax;

use Symfony\Component\HttpFoundation\JsonResponse;

class AjaxResponse extends JsonResponse
{
    /**
     * @param mixed $data    The response data
     * @param int   $status  The response status code
     * @param array $headers An array of response headers
     * @param bool  $json    If the data is already a JSON string
     */
    public function __construct($data = null, $status = 200, $headers = array(), $json = false)
    {
        $data = [
            'status' => true,
            'data' => $data,
        ];

        parent::__construct($data, $status, $headers, $json);
    }
}
