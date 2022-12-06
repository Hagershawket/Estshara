<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AboutResource extends JsonResource
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
            'about_us'        => $this->about_us ?? '',
            'whatsApp'        => $this->whatsApp ?? '',
            'facebook'        => $this->social_media[0] ?? '',
            'twitter'         => $this->social_media[1] ?? '',
            'youtube'         => $this->social_media[2] ?? '',
            'instagram'       => $this->social_media[3] ?? '',
        ];
    }
}
