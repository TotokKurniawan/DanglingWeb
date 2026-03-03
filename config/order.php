<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Order Cancellation Settings
    |--------------------------------------------------------------------------
    |
    | buyer_cancel_timeout_minutes:
    |   Batas waktu maksimal (dalam menit) bagi buyer untuk bisa melakukan
    |   pembatalan order secara manual sejak order dibuat.
    |   Nilai null akan menonaktifkan batas waktu (hanya status yang dicek).
    |
    */

    /*
     * Batas waktu (menit) buyer boleh membatalkan order setelah dibuat.
     * null = tidak ada batas waktu (hanya status yang dicek).
     * Contoh: ORDER_BUYER_CANCEL_TIMEOUT_MINUTES=10
     */
    'buyer_cancel_timeout_minutes' => env('ORDER_BUYER_CANCEL_TIMEOUT_MINUTES', null),
];

