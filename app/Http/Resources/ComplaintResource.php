<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ComplaintResource extends JsonResource
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
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'date' => $this->date,
            'province' => $this->province,
            'city' => $this->city,
            'user' => $this->visibility == 'anonim' && $request->user()->role->slug == 'masyarakat' ? 0 : [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'username' => $this->user->username,
                'profile' => $this->user->profile,
            ],
            'files' => ComplaintFileResource::collection($this->files),
            'total_supports' => $this->supports()->count(),
            'is_spported' => $this->supports()->where('user_id', $this->user->id)->first() ? 0 : 1,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
