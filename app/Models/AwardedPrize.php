<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AwardedPrize extends Model
{
    use HasFactory;

    protected $table = 'awarded_prizes';
    protected $fillable = ['prize_id', 'awarded_count', 'percentage'];

}
