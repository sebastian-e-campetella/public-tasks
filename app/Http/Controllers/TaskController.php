<?php

namespace App\Http\Controllers;

use Czim\JsonApi\Requests\JsonApiRequest;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Task;
use JsonApiEncoder;
use Validator;
use Cache;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $map_names = function($n){
            return $n[2];
        };
        
        $columns = [];
        if ( (string)jsonapi_query()->getFilterValue('id') != "") \
            array_push($columns, ['id' , "=", (string)jsonapi_query()->getFilterValue('id')]);
        if (jsonapi_query()->getFilterValue('completed') != "") \
            array_push($columns, ['completed' , "=", strtolower(jsonapi_query()->getFilterValue('completed')) == 'true' ? true : false]) ;
        if (jsonapi_query()->getFilterValue('due_date')) \
            array_push($columns, ['due_date'  , "=", strtotime(jsonapi_query()->getFilterValue('due_date'))]);
        if (jsonapi_query()->getFilterValue('created_at')) \
            array_push($columns, ['created_at', "=", strtotime(jsonapi_query()->getFilterValue('created_at'))]);
        if (jsonapi_query()->getFilterValue('created_at') != "") \
            array_push($columns, ['updated_at', "=", strtotime(jsonapi_query()->getFilterValue('updated_at'))]) ;
         
        $page = (int)jsonapi_query()->getPageNumber();

        $names = array_map($map_names, $columns);
        $mem_key = 'tasks-'.$page.'-'.implode("-", $names);
        var_dump($columns);
        
        $tasks = Cache::remember($mem_key, env('MEMCACHED_TIME') || 1 , function () use($columns) {
            return Task::where($columns)->orderBy('created_at','desc')->jsonPaginate();
        });
        var_dump($tasks);
        // Instantiate the encoder
        $encoder = app(\Czim\JsonApi\Contracts\Encoder\EncoderInterface::class);
        $encoder->setRequestedIncludes([ 'tasks' ]);
        $json_tasks = [];
        foreach ($tasks as $task) {
            array_push($json_tasks,["id" => $task->id, "type" => Str::lower(class_basename($task)), "attributes" => $task]);
        }
        var_dump($json_tasks);;
        $response_tasks = $tasks->toArray();
        $response_tasks["data"] = $json_tasks;
        return jsonapi_response( $response_tasks);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make(jsonapi_request_create()->data, $this->rules());
        if ($validator->fails()){
            return jsonapi_response( ["errors" => $validator->errors()],500);
        }else{
            $task = Task::create(jsonapi_request_create()->data["attributes"]);
            $encoder = app(\Czim\JsonApi\Contracts\Encoder\EncoderInterface::class);
            $data = $encoder->encode(["attributes" => $task]);
            $data["data"]["id"] = $task->id;
            $data["data"]["type"] = Str::lower(class_basename($task) );
            return jsonapi_response( $data , 201);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      if ($id != null) {
          $encoder = app(\Czim\JsonApi\Contracts\Encoder\EncoderInterface::class);
          $task = Cache::remember($id, env('MEMCACHED_TIME') || 1, function () use ($id){
              return Task::findOrFail($id);
          });
          return jsonapi_response($encoder->encode($task->toArray()));
      }else{
          return jsonapi_response(["status" => "404", "errors" => [ "title" => "Not found", "detail" => "not found"]],404);
      }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      $task = Task::findOrFail($id);
      $validator = Validator::make(jsonapi_request_create()->data, $this->rules());
      if ($validator->fails()){
          return \Response::json($validator->errors(),500);
      }else{
          $task->update(jsonapi_request_create()->data["attributes"]);
          $encoder = app(\Czim\JsonApi\Contracts\Encoder\EncoderInterface::class);
          $data = $encoder->encode(["attributes" => $task]);
          $data["data"]["id"] = $task->id;
          $data["data"]["type"] = Str::lower(class_basename($task) );
          return jsonapi_response( $data );
      }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $task = task::findorfail($id);
      $task->delete();
      return \Response::json(null,204);
    }

    public function rules()
    {
        return [
            'attributes.title'        => 'required|string',
            'attributes.due_date'     => 'required|date',
            'attributes.description'  => 'string',
            'attributes.completed'    => 'boolean',
        ];
    }


}
