<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BroadcastRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
                'min:3'
            ],
            'message' => [
                'required',
                'string',
                'max:5000',
                'min:10'
            ],
            'type' => [
                'required',
                Rule::in(['promo', 'update', 'maintenance', 'announcement'])
            ],
            'target_type' => [
                'required',
                Rule::in(['all', 'specific'])
            ],
            'target_users' => [
                'nullable',
                'array',
                'max:1000', // Limit to 1000 users max
                'required_if:target_type,specific'
            ],
            'target_users.*' => [
                'integer',
                'exists:users,id'
            ],
            'priority' => [
                'required',
                Rule::in([1, 2, 3])
            ],
            'scheduled_at' => [
                'nullable',
                'date',
                'after:now',
                'before:' . now()->addYear()->toDateString() // Max 1 year in future
            ],
            'send_now' => [
                'boolean'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Broadcast title is required.',
            'title.min' => 'Title must be at least 3 characters.',
            'title.max' => 'Title cannot exceed 255 characters.',
            'message.required' => 'Broadcast message is required.',
            'message.min' => 'Message must be at least 10 characters.',
            'message.max' => 'Message cannot exceed 5000 characters.',
            'type.required' => 'Please select a broadcast type.',
            'type.in' => 'Invalid broadcast type selected.',
            'target_type.required' => 'Please select target audience.',
            'target_type.in' => 'Invalid target type selected.',
            'target_users.required_if' => 'Please select at least one user for specific targeting.',
            'target_users.max' => 'Cannot target more than 1000 users at once.',
            'target_users.*.exists' => 'One or more selected users do not exist.',
            'priority.required' => 'Please select a priority level.',
            'priority.in' => 'Invalid priority level selected.',
            'scheduled_at.after' => 'Scheduled time must be in the future.',
            'scheduled_at.before' => 'Cannot schedule more than 1 year in advance.'
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Additional validation logic
            if ($this->target_type === 'specific' && empty($this->target_users)) {
                $validator->errors()->add('target_users', 'Please select at least one user for specific targeting.');
            }

            // Validate that target users are not banned and not admins
            if ($this->target_type === 'specific' && !empty($this->target_users)) {
                $invalidUsers = \App\Models\User::whereIn('id', $this->target_users)
                    ->where(function ($query) {
                        $query->where('is_banned', true)
                              ->orWhere('role', 'admin');
                    })
                    ->count();

                if ($invalidUsers > 0) {
                    $validator->errors()->add('target_users', 'Cannot target banned users or administrators.');
                }
            }

            // Rate limiting validation
            if ($this->send_now) {
                $recentBroadcasts = \App\Models\Broadcast::where('created_by', auth()->id())
                    ->where('created_at', '>=', now()->subHour())
                    ->count();

                if ($recentBroadcasts >= 5) {
                    $validator->errors()->add('send_now', 'You can only send 5 broadcasts per hour.');
                }
            }
        });
    }
}
