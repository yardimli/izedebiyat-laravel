<?php

return [
    'frontend_model' => env('LLM_FRONTEND_MODEL', 'anthropic/claude-3.5-haiku:beta'),
    'backend_model' => env('LLM_BACKEND_MODEL', 'openai/gpt-5.6-luna'),
    'cron_model' => env('LLM_CRON_MODEL', 'openai/gpt-5.6-luna'),
];
