<?php

namespace App\Http\Controllers;

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
       $f_name = function($n){
            return $n[2];
        };
        // Get a specific filter key value, if it is present (with a default fallback).
        // Get the page number.
        global $page, $columns;
        $hash = [ 
            'id'         => (string)jsonapi_query()->getFilterValue('id'),
            'completed'  => strtolower(jsonapi_query()->getFilterValue('completed')) == 'true' ? true : false,
            'due_date'   => strtotime(jsonapi_query()->getFilterValue('due_date')),
            'created_at' => strtotime(jsonapi_query()->getFilterValue('created_at')),
            'updated_at' => strtotime(jsonapi_query()->getFilterValue('updated_at')),
          ];
        $page = (int)jsonapi_query()->getPageNumber();
        $columns = [];
        foreach ($hash as $key => $val){
          if ($val != null){
            array_push($columns, [$key,"=",$val] );
          }
        }

        $names = array_map($f_name, $columns);
        $mem_key = 'tasks-'.$page.'-'.implode("-", $names);
        $tasks = Cache::remember($mem_key, env('MEMCACHED_TIME'), function () {
            return Task::where($GLOBALS['columns'])->orderBy('created_at','desc')->jsonPaginate(5);
        });

        // Instantiate the encoder
        $encoder = app(\Czim\JsonApi\Contracts\Encoder\EncoderInterface::class);
        $encoder->setRequestedIncludes([ 'tasks' ]);
        $json_tasks = [];
        foreach ($tasks as $task) {
            array_push($json_tasks,["id" => $task->id, "type" => Str::lower(class_basename($task)), "attributes" => $task]);
        }

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
      $root = jsonapi_request()->data;
      $validator = Validator::make(jsonapi_request_create()->data, $this->rules());
      if ($validator->fails()){
          return \Response::json($validator->errors(),500);
      }else{
          $task = Task::create(jsonapi_request_create()->data);
#          return jsonapi_response(jsonapi_request_create());
          $encoder = app(\Czim\JsonApi\Contracts\Encoder\EncoderInterface::class);
          $data = $encoder->encode(["attributes" => $task]);
          $data["data"]["id"] = $task->id;
          $data["data"]["type"] = Str::lower(class_basename($task) );
          return jsonapi_response( $data );
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
        $task = Cache::remember($id, env('MEMCACHED_TIME'), function () {
             return Task::findOrFail(jsonapi_request()->id);
        });
        return jsonapi_response($encoder->encode($task->toArray()));

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
            // Attribute rules
            'attributes.title'        => 'required|string',
            'attributes.due_date'     => 'required|date',
            'attributes.description'  => 'required|string',
        ];
    }


}