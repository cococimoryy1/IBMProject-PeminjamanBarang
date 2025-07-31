@extends('layout.auth')
@section('title', 'Peminjaman Barang - Daftar')
@section('form')
<form method="POST" action="{{ route('register') }}">
    @csrf
    <input type="text" name="name" placeholder="Nama Pengguna" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Kata Sandi" required>
    <button type="submit">Daftar</button>
</form>
<div class="switch">
    <p>Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
</div>
@endsection
