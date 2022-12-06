<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if($this->flag == 1)
        {
            return [
                'id'            => $this->id,
                'title'         => $this->title ?? '',
                'body'          => $this->body  ?? '',
                'image'         => $this->image ? asset('/') . $this->image : '',
                'user_id'       => $this->user->name ?? '',
                'star_num'      => $this->star_num ?? 0,
                'share_num'     => $this->share_num ?? 0,
            ];
        }
        elseif($this->flag == 0)
        {
            return [
                'id'            => $this->id,
                'title'         => $this->title ?? '',
                'image'         => $this->image ? asset('/') . $this->image : '',
                'user_id'       => $this->user->name ?? '',
            ];
        }
    }
}
