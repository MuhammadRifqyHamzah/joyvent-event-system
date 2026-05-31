<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'key',
        'value'
    ];

    /**
     * Get a setting value by key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getValue(string $key, $default = null)
    {
        $setting = static::find($key);
        return $setting ? $setting->value : $default;
    }

    /**
     * Set/update a setting value by key.
     *
     * @param string $key
     * @param string|null $value
     * @return Setting
     */
    public static function setValue(string $key, ?string $value)
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
