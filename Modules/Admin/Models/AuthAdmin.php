<?php
namespace Modules\Admin\Models;
use DateTimeInterface;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
//jwt-auth secret [ohehOIoX7ijxB49o8bpCDk70N532OuXJTlijpa6QOr3qrxKIiyex8Adxy8Tpl0AC] set successfully.
class AuthAdmin extends Authenticatable implements JWTSubject
{
    use Notifiable;
	protected $guard = 'auth_admin';
	protected $hidden = [
		'password'
	];
	/**
	 * @name jwt标识
	 * @description
	 * @author Winston
	 * @date 2021/12/26 11:15
	 **/
	public function getJWTIdentifier()
    {
        return $this->getKey();
    }
	/**
	 * @name jwt自定义声明
	 * @description
	 * @author Winston
	 * @date 2021/12/26 11:15
	 **/
    public function getJWTCustomClaims()
    {
        return [];
    }
	/**
	 * @name 更新时间为null时返回
	 * @description
	 * @author Winston
	 * @date 2021/12/26 11:15
	 **/
    public function getUpdatedAtAttribute($value)
    {
        return $value?$value:'';
    }
	/**
	 * @name  关联权限组表   多对一
	 * @description
	 * @author Winston
	 * @date 2021/12/26 11:15
	 **/
	 public function auth_groups()
    {
        return $this->belongsTo('Modules\Admin\Models\AuthGroup','group_id','id');
    }
    /**
     * @name  关联平台项目表   多对一
     * @description
     * @author Winston
     * @date 2021/12/26 11:15
     **/
    public function auth_projects()
    {
        return $this->belongsTo('Modules\Admin\Models\AuthProject','project_id','id');
    }
    /**
     * @name 时间格式传唤
     * @description
     * @author Winston
     * @date 2021/12/26 11:15
     **/
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
