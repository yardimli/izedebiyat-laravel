<?php

namespace Tests\Feature;

use App\Http\Controllers\AiGenerationController;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class StandaloneChatRemovalTest extends TestCase
{
    public function test_standalone_chat_routes_are_not_registered(): void
    {
        foreach (Route::getRoutes() as $route) {
            $this->assertFalse(str_starts_with($route->uri(), 'sohbet'));
            $this->assertNotContains($route->uri(), ['send-llm-prompt', 'check-llms-json']);
        }
        foreach (['chat', 'chat.create-session', 'chat.destroy', 'send-llm-prompt'] as $name) {
            $this->assertFalse(Route::has($name));
        }
    }

    public function test_shared_ai_generation_routes_remain_available(): void
    {
        foreach ([
            'articles.generate-category' => 'generateCategory',
            'articles.generate-description' => 'generateDescription',
            'articles.generate-keywords' => 'generateKeywords',
            'book-reviews.generate-category' => 'generateBookCategory',
            'book-reviews.generate-keywords' => 'generateBookKeywords',
        ] as $name => $method) {
            $this->assertSame(AiGenerationController::class.'@'.$method, Route::getRoutes()->getByName($name)->getActionName());
            $this->assertTrue(method_exists(AiGenerationController::class, $method));
        }
    }
}
