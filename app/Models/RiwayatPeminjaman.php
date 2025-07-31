<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatPeminjaman extends Model
{
    protected $table = 'riwayat_peminjamans';
    protected $fillable = ['peminjaman_id', 'nama_barang', 'nama_peminjam', 'tanggal_pinjam', 'tanggal_kembali', 'status'];
    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_kembali' => 'date',
    ];

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }
}
