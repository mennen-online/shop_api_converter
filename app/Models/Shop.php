<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['name', 'status', 'url', 'credentials', 'user_id'];

    protected $searchableFields = ['*'];

    protected $casts = [
        'credentials' => 'object',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function allShopData()
    {
        return $this->hasMany(ShopData::class);
    }

    public function endpoints()
    {
        return $this->hasMany(Endpoint::class);
    }

    public function entities()
    {
        return $this->hasMany(Entity::class);
    }
}
