<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingRule extends Model {
    protected $fillable = ['day_type','price','effective_date','created_by'];
    protected $casts = ['effective_date' => 'date'];
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public static function getCurrentPrice(string $dayType): float {
        return static::where('day_type', $dayType)
            ->where('effective_date', '<=', today())
            ->latest('effective_date')->value('price') ?? 0;
    }
}
