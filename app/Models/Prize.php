<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Prize extends Model
{

    protected $guarded = ['id'];

    public function awardedPrizes()
    {
        return $this->hasMany(AwardedPrize::class);
    }

    public  static function nextPrize($prize, $number_of_total_prizes, &$awardedPrizes, &$awardedPrizesPercent)
    {

        $awardedPrizes[$prize->id] = number_format($number_of_total_prizes * ($prize->probability / Prize::sum('probability')), 0);
        $awardedPrizesPercent[$prize->id] = number_format(($awardedPrizes[$prize->id] / $number_of_total_prizes) * 100, 2);

        AwardedPrize::updateOrCreate(
            ['prize_id' => $prize->id],
            ['awarded_count' => $awardedPrizes[$prize->id], 'percentage' => $awardedPrizesPercent[$prize->id]]
        );
    }
}