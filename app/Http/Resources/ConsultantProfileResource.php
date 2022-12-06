<?php

namespace App\Http\Resources;

use App\Models\ConsultantType;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class ConsultantProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data['experience']   = User::where('id', $this->id)->first()->experience ?? '';
        $data['education']    = User::where('id', $this->id)->first()->education ?? '';
        $data['message']      = ConsultantTypeResource::collection(ConsultantType::where('consultant_id',$this->id)->where('name', 'message')->get());
        $data['call']         = ConsultantTypeResource::collection(ConsultantType::where('consultant_id',$this->id)->where('name', 'call')->get());
        $data['video']        = ConsultantTypeResource::collection(ConsultantType::where('consultant_id',$this->id)->where('name', 'video')->get());
        return $data;
    }
}
