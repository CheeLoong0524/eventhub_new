<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InquiryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'inquiry_id' => $this->inquiry_id,
            'name' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
            'status' => $this->status,
            'admin_reply' => $this->admin_reply,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'resolved_at' => $this->resolved_at?->format('Y-m-d H:i:s'),
            
            // User information (if available)
            'user' => $this->when($this->relationLoaded('user') && $this->user, [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'role' => $this->user->role,
                'created_at' => $this->user->created_at?->format('Y-m-d H:i:s'),
            ]),
            
            // Resolver information (if available)
            'resolver' => $this->when($this->relationLoaded('resolver') && $this->resolver, [
                'id' => $this->resolver?->id,
                'name' => $this->resolver?->name,
                'email' => $this->resolver?->email,
            ]),
            
            // Computed fields
            'status_label' => $this->getStatusLabel(),
            'status_badge_color' => $this->getStatusBadgeColor(),
            'time_ago' => $this->created_at?->diffForHumans(),
            'is_resolved' => $this->status === 'resolved' || $this->status === 'closed',
            'has_admin_reply' => !empty($this->admin_reply),
        ];
    }

    /**
     * Get status label
     */
    private function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => 'Pending',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get status badge color
     */
    private function getStatusBadgeColor(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'resolved' => 'success',
            'closed' => 'secondary',
            default => 'secondary',
        };
    }
}
