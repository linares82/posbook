<?php

namespace App\Models;

use App\Traits\AuditTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class Movement extends Model
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

    protected $fillable = ['plantel_id','reason_id','type_movement_id','product_id','costo','precio',
    'cantidad_entrada','cantidad_salida','order_sales_line_id', 'usu_alta_id', 'usu_mod_id', 'usu_delete_id'];

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

	public function plantel()
    {
        return $this->hasOne('App\Models\Plantel','id','plantel_id');
    }

	public function product()
    {
        return $this->hasOne('App\Models\Product','id','product_id');
    }

	public function typeMovement()
    {
        return $this->hasOne('App\Models\TypeMovement','id','type_movement_id');
    }

	public function reason()
    {
        return $this->hasOne('App\Models\Reason','id','reason_id');
    }
	
}
