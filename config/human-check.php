<?php

return [
    'enabled' => env('HUMAN_CHECK_ENABLED', true),
    'countries' => ['CN', 'SG', 'VN', 'BR', 'HK'],
    'database' => env('GEOIP_DATABASE', '/usr/share/GeoIP/GeoLite2-Country.mmdb'),
    'trusted_proxies' => array_values(array_filter(array_map('trim', explode(',', env('HUMAN_CHECK_TRUSTED_PROXIES', ''))))),
    'challenge_seconds' => 600,
    'verified_seconds' => 7200,
];
