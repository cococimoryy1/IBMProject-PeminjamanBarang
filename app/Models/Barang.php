<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barangs';
    protected $fillable = ['nama_barang', 'kategori', 'stok', 'lokasi_penyimpanan', 'deskripsi'];

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class, 'barang_id');
    }
}
