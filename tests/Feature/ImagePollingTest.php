<?php

namespace Tests\Feature;

use App\Http\Controllers\ImageController;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Tests\WriterTestCase;

class ImagePollingTest extends WriterTestCase
{
    private function controller(array $responses): ImageController
    {
        $this->actingAs(User::factory()->create());
        config(['image_generation.fal_key' => 'fake-key', 'image_generation.poll_limit' => 40, 'image_generation.poll_interval' => 3]);
        $controller = \Mockery::mock(ImageController::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $controller->shouldReceive('enhanceImagePrompt')->once()->andReturn(['error' => false, 'content' => 'A landscape']);
        $controller->shouldReceive('imageClient')->once()->andReturn(new Client(['handler' => HandlerStack::create(new MockHandler($responses))]));
        return $controller;
    }

    public function test_pending_generation_returns_json_instead_of_empty_200(): void
    {
        $responses = [new Response(200, [], json_encode(['status_url' => 'https://queue.fal.run/status']))];
        for ($i = 0; $i < 40; $i++) $responses[] = new Response(200, [], json_encode(['status' => 'IN_PROGRESS', 'request_id' => 'pending-job']));
        $controller = $this->controller($responses);
        $controller->shouldReceive('waitForImagePoll')->with(3)->times(39);
        $response = $controller->makeImage(Request::create('/image-gen', 'POST', ['user_prompt' => 'Test']));
        $this->assertSame(504, $response->getStatusCode());
        $this->assertFalse($response->getData(true)['success']);
        $this->assertSame(40, $response->getData(true)['checks']);
        $this->assertSame('pending-job', $response->getData(true)['request_id']);
    }

    public function test_completed_result_without_image_returns_a_clear_error(): void
    {
        $controller = $this->controller([
            new Response(200, [], json_encode(['status_url' => 'https://queue.fal.run/status'])),
            new Response(200, [], json_encode(['status' => 'COMPLETED', 'response_url' => 'https://queue.fal.run/result'])),
            new Response(200, [], json_encode(['images' => []])),
        ]);
        $controller->shouldNotReceive('waitForImagePoll');
        $response = $controller->makeImage(Request::create('/image-gen', 'POST', ['user_prompt' => 'Test']));
        $this->assertSame(502, $response->getStatusCode());
        $this->assertFalse($response->getData(true)['success']);
    }
}
