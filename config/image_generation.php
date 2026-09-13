<?php

return [
    'fal_endpoint' => env('FAL_IMAGE_MODEL_ENDPOINT', 'https://queue.fal.run/fal-ai/qwen-image'),
    'poll_interval' => max(1, (int) env('FAL_POLL_INTERVAL_SECONDS', 3)),
    'poll_limit' => max(1, (int) env('FAL_POLL_LIMIT', 40)),
    'request_timeout' => max(60, (int) env('IMAGE_GENERATION_TIMEOUT_SECONDS', 360)),
    'fal_key' => env('FAL_API_KEY'),
    'prompt_model' => env('IMAGE_PROMPT_LLM_MODEL', 'openai/gpt-5.6-luna'),
];
