<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model {
    protected $fillable = ['user_id','type','template','payload','status','reference_id','sent_at'];
    protected $casts = ['payload'=>'array','sent_at'=>'datetime'];
    public function user() { return $this->belongsTo(User::class); }
}
