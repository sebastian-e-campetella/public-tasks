<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    public function testFetchesAllTasks()
    {
        $response = $this->call('GET', 'api/tasks');
        var_dump($response); 
        $data = $this->parseJson($response);
        $this->assertIsJson($data);
        $this->assertInternalType('array', $data->tasks);
    }

    public function testCreatesPhoto()
    {
        $photo = [
            'caption' => 'My new photo',
            'path'    => 'foo.jpg',
            'user_id' => 1
        ];
        $response = $this->call('POST', 'api/v1/photos', $photo);
        $data = $this->parseJson($response);
        $this->assertEquals(false, $data->error);
        $this->assertEquals('Photo was created', $data->message);
    }

    protected function parseJson(Illuminate\Http\JsonResponse $response)
    {
        return json_decode($response->getContent());
    }

    protected function assertIsJson($data)
    {
        $this->assertEquals(0, json_last_error());
    }


}
