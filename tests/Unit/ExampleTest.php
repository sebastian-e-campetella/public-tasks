<?php

namespace Tests\Unit;

use Tests\TestCase;
use Czim\JsonApi\Http\Requests\JsonApiRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\Task as Task;
use DateTime;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    use DatabaseMigrations;
    use WithoutMiddleware;

    protected $task;

    public function setUp() {
        parent::setUp();
        $this->task = factory(Task::class)->create();
    }

    public function testFetchesAllTasks()
    {
        $response = $this->call('GET', 'api/tasks', [
            'Accept' => 'application/vnd.api+json',
            'Content-type' => 'application/vnd.api+json',
          ])->send();
        $this->assertNotNull(json_decode($response->getContent())->data);
    }

    public function testFetchOneTask()
    {
        $response = $this->get('api/tasks', [ "data" => 
            [
              "id" => $this->task->id,
            ]
        ])->send();
        $this->assertNotNull(json_decode($response->getContent())->data);
    }

    public function testWrongParametersToCreateTask()
    {
        $data_wrong =[
            'data' => [
                'attributes'   => [
                    'title'      => 'right title',
                    'description'=> 'a short description',
                    'due_dae'   => new datetime(),
                ],
            ],
        ];

        $response = $this->post('api/tasks', $data_wrong)->send();
        $response_data_wrong =  json_decode($response->getContent());
        $this->assertNotNull($response_data_wrong->errors);
    }


    public function testDeleteTask(){
        $response = $this->delete('api/tasks/'.$this->task->id);
        $data =  json_decode($response->getContent());
        $this->assertEquals($response->getStatusCode(),204);
    }

    public function testCreateTask()
    {
        $data =[
            'data' => [
                'attributes'   => [
                    'due_date'   => '2018-10-10',
                    'title'      => 'right title',
                    'description'=> 'a short description',
                ],
            ],
        ];

        $response = $this->call('POST','api/tasks', $data,[
            'Accept' => 'application/vnd.api+json',
            'Content-type' => 'application/vnd.api+json',
          ]);
        $response_data = $response->getContent();
        $this->assertNotNull($response_data);
    }
}
