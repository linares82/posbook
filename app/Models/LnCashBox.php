<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Venturecraft\Revisionable\RevisionableTrait;

class LnCashBox extends Model
{
    use HasFactory, SoftDeletes, RevisionableTrait;

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

	protected $revisionCreationsEnabled = true;

    protected $fillable = ['cash_box_id','product_id','quantity','precio','total','usu_alta_id', 'usu_mod_id', 'usu_delete_id','movement_id'];

	protected $dates = ['deleted_at'];

	

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

	public function cashBox()
	{
		return $this->belongsTo('App\Models\CashBox');
	} // end

	public function product()
	{
		return $this->belongsTo('App\Models\Product');
	} // end

	public function paymentMethod()
	{
		return $this->belongsTo('App\Models\PaymentMethod');
	} // end

	public function movement()
	{
		return $this->belongsTo('App\Models\Movement');
	} // end

	
}
