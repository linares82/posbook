<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Venturecraft\Revisionable\RevisionableTrait;

class CashBox extends Model
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

    protected $fillable = ['plantel_id','customer','fecha','reference','st_cash_box_id','total', 'usu_alta_id', 'usu_mod_id', 'usu_delete_id','matricula','bnd_entregado','bnd_referencia_revisada'];

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

    public function stCashBox()
	{
		return $this->hasOne('App\Models\StCashBox', 'id', 'st_cash_box_id');
	} // end

	public function plantel()
	{
		return $this->hasOne('App\Models\Plantel', 'id', 'plantel_id');
	} // end

	public function lnCashBoxes()
	{
		return $this->hasMany('App\Models\LnCashBox');
	} // end

	public function payments()
	{
		return $this->hasMany('App\Models\Payment');
	} // end

}


