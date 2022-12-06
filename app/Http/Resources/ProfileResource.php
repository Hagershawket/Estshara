<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Appointment;
use App\Http\Resources\AppointmentResource;

class ProfileResource extends JsonResource
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
            'id'                => $this->id,
            'image'             => $this->image ? asset('/') . $this->image : '',
            'name'              => $this->name,
            'email'             => $this->email,
            'birth_of_year'     => $this->birth_of_year ?? 0,
            'phone'             => $this->phone ?? '',
        ];
        if ($this->type == 1) {
            $data['additional_phone'] =  $this->additional_phone ?? '';
            $data['years_of_experience'] = $this->years_of_experience ?? 0;
            $data['gender'] = $this->gender ?? '';
            $data['education'] = $this->education ?? '';
            $data['experience'] = $this->experience ?? '';

            $education_certificates = explode(',', $this->education_certificate);
            foreach($education_certificates as $key => $img){
                $data['education_certificate'][$key] = asset('/') . $img;
            }
            $experience_certificates = explode(',', $this->experience_certificate);
            foreach($education_certificates as $key => $img){
                $data['experience_certificate'][$key] = asset('/') . $img;
            }
            $data['subcategory'] = $this->subcategory ? $this->subcategory->name : '';
            $data['types'] = AppointmentResource::collection(Appointment::where('consultant_id' , $request->id)->get());
        }
        return $data;
    }
}
