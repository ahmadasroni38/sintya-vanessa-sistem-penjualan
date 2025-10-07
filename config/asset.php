<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Asset QR Code Base URL
    |--------------------------------------------------------------------------
    |
    | This value is the base URL used when generating QR codes for assets.
    | When a QR code is scanned, it will redirect to this URL + asset ID.
    | This allows for easy asset tracking and information access.
    |
    */

    'qr_base_url' => env('ASSET_QR_BASE_URL', env('APP_URL') . '/assets'),

    /*
    |--------------------------------------------------------------------------
    | Asset QR Code URL Type
    |--------------------------------------------------------------------------
    |
    | Determines what identifier to use in QR code URL:
    | - 'id': Use asset database ID (e.g., /assets/123)
    | - 'code': Use asset code (e.g., /assets/LAPTOP01)
    |
    */

    'qr_url_type' => env('ASSET_QR_URL_TYPE', 'id'),

    /*
    |--------------------------------------------------------------------------
    | Public Asset View Settings
    |--------------------------------------------------------------------------
    |
    | Settings for public asset information view (when QR code is scanned
    | by non-authenticated users)
    |
    */

    'public_view' => [
        'enabled' => env('ASSET_PUBLIC_VIEW_ENABLED', true),
        'show_price' => env('ASSET_PUBLIC_SHOW_PRICE', false),
        'show_serial' => env('ASSET_PUBLIC_SHOW_SERIAL', false),
    ],

];
