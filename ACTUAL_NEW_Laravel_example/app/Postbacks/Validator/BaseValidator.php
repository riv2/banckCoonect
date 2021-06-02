<?php

namespace App\Postbacks\Validator;


use GuzzleHttp\Psr7\Response;

class BaseValidator
{

    protected Response $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return boolean
     */
    public function validate()
    {
        return $this->response->getStatusCode() == 200;
    }
}
