<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
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
            'consultant_name'    => $this->consultant->name  ?? '',
            'date'               => $this->appointment->date ?? '',
            'time_from'          => $this->appointment->time_from ?? '',
            'time_to'            => $this->appointment->time_to ?? '',
            'cost'               => $this->type->cost ?? 0,
        ];
        switch ($this->type->name) {
            case 'message':
                $data['type'] = 'محادثة نصية';
                break;
            case 'call':
                $data['type'] = 'محادثة هاتفية';
                break;
            case 'video':
                $data['type'] = 'محادثة فيديو';
                break;
            default:
                $data['type'] = '';
        }
        return $data;
    }
}
