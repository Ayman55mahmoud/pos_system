<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order extends Model
{
    //
    use HasFactory;

   protected $fillable = ['table_id','phone','order_type','address','total_price','user_id','order_number'];


    public function table(){
        return $this->belongsTo(table::class,'table_id');
    }

    public function items()
{
    return $this->hasMany(order_itme::class);
}

}
