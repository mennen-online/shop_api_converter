<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Entity extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['name', 'shop_id'];

    protected $searchableFields = ['*'];

    public function entityFields()
    {
        return $this->hasMany(EntityField::class);
    }

    public function allShopData()
    {
        return $this->hasMany(ShopData::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
