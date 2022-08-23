<?php

namespace App\Models;

use App\Models\Plantel;
use App\Models\Product;
use App\Models\OrderSale;
use App\Traits\AuditTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderSalesLine extends Model
{
    use HasFactory, SoftDeletes, AuditTrait;

    protected $fillable = ['order_sale_id','plantel_id','product_id','cantidad','contacto','bnd_entrada_registrada', 
    'usu_alta_id', 'usu_mod_id', 'usu_delete_id'];

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

	public function orderSale(){
		return $this->belongsTo(OrderSale::class);
	}

	public function plantel(){
		return $this->belongsTo(Plantel::class);
	}

	public function product(){
		return $this->belongsTo(Product::class);
	}
}
