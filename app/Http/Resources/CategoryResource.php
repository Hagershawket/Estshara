<?php

namespace App\Http\Resources;

use App\Models\ConsultantCategory;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'image'         => $this->image ? asset('/') . $this->image : '',
            'count'         => ConsultantCategory::where('category_id', $this->id)->count(),
        ];
    }
}
