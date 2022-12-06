<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = [
            'id'                 => $this->id,
            'time_from'          => $this->time_from,
            'time_to'            => $this->time_to,
        ];  

        if($this->duration <= $this->min_remain)
            $data['status'] = 1;
        else
            $data['status'] = 0;
        return $data; 
    }
}
