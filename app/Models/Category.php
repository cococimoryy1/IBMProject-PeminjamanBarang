<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = ['name', 'description'];

    public function barangs()
    {
        return $this->hasMany(Barang::class, 'kategori', 'name');
    }
}
