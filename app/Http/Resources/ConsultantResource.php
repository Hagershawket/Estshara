<?php

namespace App\Http\Resources;

use App\Models\User;
use App\Models\Review;
use App\Models\Subcategory;
use Illuminate\Http\Resources\Json\JsonResource;

class ConsultantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if($this->flag == 0)
        {
            return [
                'id'                  => $this->id,
                'consultant_name'     => User::where('id', $this->consultant_id)->first()->name,
                'subcategory'         => Subcategory::where('id', $this->subcategory_id)->first()->name,
                'image'               => User::where('id', $this->consultant_id)->first()->image ? asset('/').User::where('id', $this->consultant_id)->first()->image : '',
                'rating'              => number_format(Review::where('consultant_id', $this->consultant_id)->avg('star_num'),1) ?? '0.0',
                'count'               => Review::where('consultant_id', $this->consultant_id)->count(),
                'reservation_count'   => 0,
    
            ];
        }

        else if($this->flag == 1)
        {
            return [
                'id'                    => $this->id,
                'consultant_name'       => User::where('id', $this->id)->first()->name,
                'subcategory'           => Subcategory::where('id', $this->subcategory_id)->first()->name,
                'image'                 => User::where('id', $this->id)->first()->image ? asset('/').User::where('id', $this->id)->first()->image : '',
                'rating'                => number_format(Review::where('consultant_id', $this->id)->avg('star_num'),1) ?? '0.0',
                'years_of_experience'   => User::where('id', $this->id)->first()->years_of_experience,
                'reservation_count'     => 1000,
    
            ];
        }
    }
}
