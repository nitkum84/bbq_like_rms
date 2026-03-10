<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model {
    protected $fillable = ['name','email','mobile','party_size','message','status','admin_reply','replied_at'];
    protected $casts = ['replied_at'=>'datetime'];
    public function scopeNew($q) { return $q->where('status','new'); }
}
