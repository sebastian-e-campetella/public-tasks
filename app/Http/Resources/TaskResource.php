<?php

namespace App\Http\Resources;

#use Illuminate\Http\Resources\Json\JsonResource;
use JsonApiResource;

class TaskResource extends JsonApiResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
#        return parent::toArray($request);
      return [
        'type'          => 'tasks',
        'id'            => (string)$this->id,
        'attributes'    => [
          'title'       => (string)$this->title,
          'description' => (string)$this->description,
          'due_date'    => strtotime($this->due_date),
          'completed'   => (boolean)$this->complete,
          'created_at'  => strtotime($this->created_at),
          'update_at'   => strtotime($this->update_at),
        ],
      ];
    }
}
