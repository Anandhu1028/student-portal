<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentCategories extends Model
{
    use HasFactory;

    public function documents()
    {
        return $this->belongsTo(Documents::class, 'doc_category_id');
    }
}
