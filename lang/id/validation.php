<?php

return [
    'required' => ':attribute wajib diisi.', 'required_if' => ':attribute wajib diisi jika :other bernilai :value.', 'required_without' => ':attribute wajib diisi jika :values kosong.',
    'string' => ':attribute harus berupa teks.', 'numeric' => ':attribute harus berupa angka.', 'integer' => ':attribute harus berupa bilangan bulat.', 'boolean' => ':attribute harus bernilai ya atau tidak.',
    'email' => ':attribute harus berupa alamat email yang valid.', 'unique' => ':attribute sudah digunakan.', 'exists' => ':attribute tidak tersedia.', 'in' => 'Pilihan :attribute tidak valid.', 'regex' => 'Format :attribute tidak valid.',
    'date' => ':attribute harus berupa tanggal yang valid.', 'after_or_equal' => ':attribute harus pada atau setelah :date.', 'before_or_equal' => ':attribute harus pada atau sebelum :date.',
    'image' => ':attribute harus berupa gambar.', 'mimes' => ':attribute harus berupa file: :values.', 'array' => ':attribute harus berupa daftar.', 'url' => ':attribute harus berupa URL HTTP/HTTPS yang valid.', 'confirmed' => 'Konfirmasi :attribute tidak cocok.', 'current_password' => 'Kata sandi saat ini tidak cocok.',
    'min' => ['numeric' => ':attribute minimal :min.', 'string' => ':attribute minimal :min karakter.', 'file' => ':attribute minimal :min KB.', 'array' => ':attribute minimal :min item.'],
    'max' => ['numeric' => ':attribute maksimal :max.', 'string' => ':attribute maksimal :max karakter.', 'file' => ':attribute maksimal :max KB.', 'array' => ':attribute maksimal :max item.'],
    'between' => ['numeric' => ':attribute harus antara :min dan :max.', 'string' => ':attribute harus antara :min dan :max karakter.', 'file' => ':attribute harus antara :min dan :max KB.', 'array' => ':attribute harus antara :min dan :max item.'],
    'gt' => ['numeric' => ':attribute harus lebih besar dari :value.'], 'lte' => ['numeric' => ':attribute tidak boleh melebihi :value.'],
    'attributes' => ['email' => 'Email', 'password' => 'Kata sandi', 'monitoring_date' => 'Tanggal monitoring', 'harvest_date' => 'Tanggal panen', 'healthy_plants' => 'Tanaman sehat', 'alive_plants' => 'Tanaman hidup', 'dead_plants' => 'Tanaman mati', 'damaged_plants' => 'Tanaman rusak', 'gross_quantity' => 'Panen kotor', 'usable_quantity' => 'Hasil layak', 'damaged_quantity' => 'Hasil rusak', 'available_quantity' => 'Jumlah tersedia', 'photos.*' => 'Foto', 'quantity' => 'Jumlah'],
];
