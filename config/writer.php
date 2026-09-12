<?php
return [
    'openrouter_key' => env('OPENROUTER_API_KEY', env('OPEN_ROUTER_KEY')),
    'demo_limit' => max(0, (float) env('WRITER_DEMO_ALLOWANCE_USD', 1.0)),
    'max_output_tokens' => 4096,
    'openrouter_url' => 'https://openrouter.ai/api/v1',
];
