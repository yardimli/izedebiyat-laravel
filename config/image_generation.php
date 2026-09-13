<?php

return [
    'fal_endpoint' => env('FAL_IMAGE_MODEL_ENDPOINT', 'https://queue.fal.run/fal-ai/qwen-image'),
    'fal_key' => env('FAL_API_KEY'),
    'prompt_model' => env('IMAGE_PROMPT_LLM_MODEL', 'openai/gpt-5.6-luna'),
];
