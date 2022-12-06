<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'type'              => $this->type == 0 ? 'مستخدم' : 'مستشار',
            'name'              => $this->name,
            'email'             => $this->email,
            'phone'             => $this->phone ?? '',
            'gender'            => $this->gender ?? '',
            'image'             => $this->image? asset('/') . $this->image : '',
            'birth_of_year'     => $this->birth_of_year ?? '',
        ];
        if ($this->type == 1) {
            $data['additional_phone'] =  $this->additional_phone ?? '';
            $data['years_of_experience'] = $this->years_of_experience ?? '';
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
        }
        $data['token'] = $this->token;
        return $data;
    }
}
