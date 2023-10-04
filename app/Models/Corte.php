<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\AuditTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Corte extends Model
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

    protected $fillable = ['account_plantel_id','fecha_inicio', 'fecha_fin', 'saldo_ingresos', 'saldo_egresos',
    'diferencia',
    'usu_alta_id', 'usu_mod_id', 'usu_delete_id'];

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

    public function accountPlantel()
	{
		return $this->hasOne('App\Models\AccountPlantel', 'id', 'account_plantel_id');
	} // end

    public function setFechaInicioAttribute($value){
		$this->attributes['fecha_inicio']= Carbon::createFromFormat('Y-m-d\TH:i:s.uZ',$value)->toDateString();
	}

    public function setFechaFinAttribute($value){
		$this->attributes['fecha_fin']= Carbon::createFromFormat('Y-m-d\TH:i:s.uZ',$value)->toDateString();
	}
}
