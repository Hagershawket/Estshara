<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
                'name'          => $this->user ? $this->user->name : '',
                'image'         => $this->user->image ? asset('/') . $this->user->image : '',
                'comment'       => $this->comment,
                'star_num'      => $this->star_num,
                'created_at'    => $this->created_at->diffForHumans(),
        ];
    }
}
