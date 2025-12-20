<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethods extends Model
{
    use HasFactory;

    protected $fillable = [
      'method_name',
      'description'
   ];

    public function payments()
    {
        return $this->hasMany(Payments::class, 'payment_method_id');
    }
}
