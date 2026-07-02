<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Batas Waktu Pembayaran (Menit)
    |--------------------------------------------------------------------------
    | Menentukan berapa lama transaksi pendaftaran event berstatus 'pending'
    | sebelum kedaluwarsa. Default: 60 menit.
    */
    'expiration_minutes' => env('PAYMENT_EXPIRATION_MINUTES', 60),
];
