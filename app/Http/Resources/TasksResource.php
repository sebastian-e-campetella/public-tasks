<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TasksResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
      #        return parent::toArray($request);
      return [
        'type'          => 'articles',
        'id'            => (string)$this->id,
        'attributes'    => [
        'title' => $this->title,
        ],
      ];
    }
}
