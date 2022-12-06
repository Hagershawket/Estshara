<?php

namespace App\Http\Resources;
use App\Models\Review;

use Illuminate\Http\Resources\Json\JsonResource;

class ConsultantReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data['count']   = Review::where('consultant_id', $this->id)->count() ?? 0;
        $data['average'] = number_format(Review::where('consultant_id', $this->id)->avg('star_num'),1) ?? '0.0';
        $data['reviews'] = ReviewResource::collection(Review::where('consultant_id',$this->id)->active()->get());
        return $data;
    }
}
