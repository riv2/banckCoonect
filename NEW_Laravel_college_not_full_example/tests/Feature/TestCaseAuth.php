<?php


namespace Tests\Feature;

use App\User;
use Tests\TestCase;

class TestCaseAuth extends TestCase
{
    /**
     * Visit the given URI with a GET request.
     * @param $uri
     * @param array $headers
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function get($uri, array $headers = [])
    {
        $user = User::where('id', '4901')->first();

        $this->actingAs($user);

        return parent::get($uri, $headers);
    }
}