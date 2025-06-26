<?php

namespace App\Http\Resources\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function getGroupDate()
    {
        $date = Carbon::parse($this->created_at);
        if ($date->isToday()) {
            $groupDate = 'Today';
        } else if ($date->isYesterday()) {
            $groupDate = 'Yesterday';
        } else {
            $groupDate = $date->format('d M y');
        }

        return $groupDate;
    }
    
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int)$this->id,
            'slug' => (string)$this->slug,
            'title' => (string)$this->title,
            'body' => (string)$this->body,
            'url' => $this->url ? (string)$this->url : null,
            'dataId' => $this->data_id ? (string)$this->data_id : null,
            'createdAt' => $this->created_at ? Carbon::parse($this->created_at)->format('h:i a') : null,
            'groupDate' => $this->getGroupDate()
        ];
    }
}
