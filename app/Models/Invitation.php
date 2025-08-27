<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $table = 'invitations';

    protected $fillable = [
        'user_id',
        'template_id',
        'bride_name',
        'groom_name',
        'wedding_date',
        'wedding_time',
        'venue',
        'location',
        'additional_notes',
        'slug',
        'cover_image'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    protected $casts = [
        'wedding_date' => 'date',
    ];
}
