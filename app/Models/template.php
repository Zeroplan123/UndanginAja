<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;
    protected $table = 'templates';

     protected $fillable = [
        'name',
        'preview_image',
        'file_path',
        'slug',
        'description',
        'cover_image',
        'html_content',
        'css_variables',
    ];

    protected $casts = [
        'css_variables' => 'array',
    ];

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    // Method untuk mendapatkan HTML dengan variabel yang sudah diganti
    public function getCompiledHtml($variables = [])
    {
        $html = $this->html_content;
        
        // Replace variabel dalam HTML
        foreach ($variables as $key => $value) {
            $html = str_replace('[' . $key . ']', $value, $html);
        }
        
        return $html;
    }

}
