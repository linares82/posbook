<?php

namespace App\Models;

use App\Models\User;
use App\Traits\AuditTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plantel extends Model
{
    use HasFactory, SoftDeletes, AuditTrait;

    protected $fillable = ['name','address','phone','director', 'usu_alta_id', 'usu_mod_id', 'usu_delete_id'];

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

    /*public function users()
    {
        return $this->belongsToMany(User::class);
    }*/
}


