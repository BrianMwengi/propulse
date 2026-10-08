<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShortlistItem extends Model
{
    protected $fillable = ['session_id', 'property_id', 'sort_order', 'notes'];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}


