<?php
// app/Traits/JalaliTimestamps.php

namespace App\Traits;

use Morilog\Jalali\Jalalian;

trait JalaliTimestamps
{
    public function getCreatedAtJalaliAttribute()
    {
        return $this->created_at ? Jalalian::fromDateTime($this->created_at)->format('Y/m/d H:i') : null;
    }

    public function getUpdatedAtJalaliAttribute()
    {
        return $this->updated_at ? Jalalian::fromDateTime($this->updated_at)->format('Y/m/d H:i') : null;
    }
}
