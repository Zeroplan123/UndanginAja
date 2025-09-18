<?php

namespace App\Rules;

use App\Models\Template;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueTemplateName implements ValidationRule
{
    protected $ignoreId;

    public function __construct($ignoreId = null)
    {
        $this->ignoreId = $ignoreId;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $query = Template::whereRaw('LOWER(name) = ?', [strtolower($value)]);
        
        if ($this->ignoreId) {
            $query->where('id', '!=', $this->ignoreId);
        }
        
        if ($query->exists()) {
            $fail('Nama template sudah digunakan (tidak case-sensitive). Silakan pilih nama yang berbeda.');
        }
    }
}
