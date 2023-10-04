<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

	public static function boot()
	{
		parent::boot();

		static::creating(function ($model) {
	            // Remember that $model here is an instance of Article
	            $model->usu_alta_id = Auth::user()->id;
				$model->usu_mod_id = Auth::user()->id;
        });

		static::updating(function ($model) {
			// Remember that $model here is an instance of Article
			$model->usu_mod_id = Auth::user()->id;
		});

		static::deleting(function ($model) {
			// Remember that $model here is an instance of Article
			$model->usu_delete_id = Auth::user()->id;
		});
	}

    protected $fillable = ['cash_box_id','payment_method_id','monto', 'fecha','st_payment_id',
    'corte_id',
    'usu_alta_id', 'usu_mod_id', 'usu_delete_id','porcentaje_descuento'];

	protected $dates = ['deleted_at'];

	public function setFechaAttribute($value){
		$this->attributes['fecha']= Carbon::createFromFormat('Y-m-d\TH:i:s.uZ',$value)->toDateString();
	}

	public function usu_alta()
	{
		return $this->hasOne('App\Models\User', 'id', 'usu_alta_id');
	} // end

	public function usu_mod()
	{
		return $this->hasOne('App\Models\User', 'id', 'usu_mod_id');
	} // end

	public function usu_delete()
	{
		return $this->hasOne('App\Models\User', 'id', 'usu_delete_id');
	} // end

	public function paymentMethod()
	{
		return $this->belongsTo('App\Models\PaymentMethod');
	} // end

	public function stPayment()
	{
		return $this->belongsTo('App\Models\StPayment');
	} // end

	public function cashBox()
	{
		return $this->belongsTo('App\Models\CashBox');
	} // end
}
