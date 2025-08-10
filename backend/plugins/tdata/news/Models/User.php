<?php namespace TData\News\Models;

use Model;
use Illuminate\Support\Facades\Hash;

/**
 * User Model
 */
class User extends Model
{
    use \October\Rain\Database\Traits\Validation;

    public $table = 'users';

    // Параметры массового заполнения
    public $fillable = [
        'login',
        'email',
        'password',
    ];

    // Правила валидации для создания через модель
    public $rules = [
        'login'    => 'required|unique:users',
        'email'    => 'required|email|unique:users',
        'password' => 'required',
    ];

    // Скрыть из JSON
    protected $hidden = ['password'];

    /**
     * Авто-хеширование пароля при setAttribute
     */
    public function setPasswordAttribute($value)
    {
        if ($value && ! Hash::needsRehash($value)) {
            $this->attributes['password'] = Hash::make($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }
}
