<?php

namespace App\Models;

use App\Models\User;
use App\Traits\AuditTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class Plantel extends Model
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

    protected $fillable = ['name','address','phone','director', 'usu_alta_id', 'usu_mod_id', 'usu_delete_id'];

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

    /*public function users()
    {
        return $this->belongsToMany(User::class);
    }*/

	public function plantel()
	{
		return $this->belongsTo('App\Models\Movement', 'id', 'plantel_id');
	} // end

    static public function plantelCmb(){
        $user_plantels=User::find(Auth::user()->id)->plantels->pluck('id');
        //dd($user_plantels);

        return Plantel::whereIn('id', $user_plantels)
        ->get()->map(fn ($plantel)=>[
            'value' => $plantel->id,
            'label' => $plantel->name,
        ]);

    }
}


