<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ProfilePic implements Rule
{
    public function passes($attribute, $value)
    {
        if (!($value instanceof \Illuminate\Http\UploadedFile)) {
            return false; 
        }

        return $value->isValid() && 
               in_array($value->extension(), ['jpg', 'jpeg', 'png', 'gif']) && 
               $value->getSize() <= 2 * 1024 * 1024;
    }

    public function message()
    {
        return 'The profile picture must be a valid image file and cannot exceed 2MB.';
    }
}
