<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documents extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'doc_category_id',
        'document_path',
        'status',
        'uploaded_by'
    ];

    public function doc_category()
    {
        return $this->belongsTo(DocumentCategories::class, 'doc_category_id');
    }
}
