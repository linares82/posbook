<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\AuditTrait;
use App\Models\OrderSalesLine;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderSale extends Model
{
    use HasFactory, SoftDeletes, AuditTrait;

    protected $fillable = ['fecha', 'usu_alta_id', 'usu_mod_id', 'usu_delete_id'];

	protected $dates = ['deleted_at'];

	public function usu_alta()
	{
		return $this->hasOne('App\User', 'id', 'usu_alta_id');
	} // end

	public function usu_mod()
	{
		return $this->hasOne('App\User', 'id', 'usu_mod_id');
	} // end

	public function usu_delete()
	{
		return $this->hasOne('App\User', 'id', 'usu_delete_id');
	} // end

	public function setFechaAttribute($value){
		$this->attributes['fecha']= Carbon::createFromFormat('Y-m-d\TH:i:s.uZ',$value)->toDateString();
	}

	public function orderSalesLines(){
		return $this->hasMany(OrderSalesLine::class);
	}
}
