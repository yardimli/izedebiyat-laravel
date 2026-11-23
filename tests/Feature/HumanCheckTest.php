<?php

namespace Tests\Feature;

use App\Services\HumanChallenge;
use App\Services\VisitorCountry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class HumanCheckTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['human-check.enabled' => true, 'session.driver' => 'array', 'cache.default' => 'array']);
        Route::middleware('web')->get('/gate-test', fn () => response('protected content'));
        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.9']);
        $this->country('CN');
    }

    private function country(?string $country): void
    {
        $this->app->instance(VisitorCountry::class, new class($country) extends VisitorCountry
        {
            public function __construct(private ?string $value) {}

            public function country(string $ip): ?string
            {
                return $this->value;
            }
        });
    }

    public function test_restricted_and_unknown_countries_are_blocked_before_content(): void
    {
        foreach (['CN', 'SG', 'VN', 'BR', 'HK', null] as $country) {
            $this->country($country);
            $this->get('/gate-test')->assertRedirect('/human-check')->assertDontSee('protected content');
        }
        $this->country('TR');
        $this->get('/gate-test')->assertOk()->assertSee('protected content');
    }

    public function test_localhost_and_disabled_gate_bypass_lookup(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '127.0.0.1'])->get('/gate-test')->assertOk();
        config(['human-check.enabled' => false]);
        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.9'])->get('/gate-test')->assertOk();
    }

    public function test_image_and_form_do_not_expose_answers_or_analytics(): void
    {
        $response = $this->get('/human-check')->assertOk()->assertDontSee('googletagmanager')->assertDontSee('matomo');
        $puzzle = session('human_check.puzzle');
        foreach (require resource_path('human-check/paragraphs.php') as $lines) {
            foreach ($lines as $line) {
                $response->assertDontSee($line);
            }
        }
        $response->assertSee($puzzle['question'])->assertSee($puzzle['image']);
        $response->assertDontSee('/human-check/image?id=');
        $this->assertArrayNotHasKey('lines', $puzzle);
        $this->assertFalse(Route::has('human-check.image'));
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
    }

    public function test_all_ten_static_images_exist_and_questions_match_their_paragraph(): void
    {
        $paragraphs = require resource_path('human-check/paragraphs.php');
        $this->assertCount(10, $paragraphs);
        $this->assertCount(10, glob(public_path('human-check-images/*.png')));
        foreach ($paragraphs as $index => $lines) {
            $path = public_path(sprintf('human-check-images/paragraph-%02d.png', $index + 1));
            $this->assertFileExists($path);
            $this->assertStringStartsWith("\x89PNG", file_get_contents($path));
            $size = getimagesize($path);
            $this->assertSame([1000, 380], [$size[0], $size[1]]);
            $this->assertCount(7, $lines);
            foreach ($lines as $line) {
                $this->assertGreaterThanOrEqual(5, count(explode(' ', $line)));
            }
        }
        $challenge = new HumanChallenge;
        for ($i = 0; $i < 30; $i++) {
            $puzzle = $challenge->create();
            preg_match('/paragraph-(\\d+)\\.png$/', $puzzle['image'], $image);
            preg_match('/^(\\d+)\\. satırdaki (\\d+)\\./u', $puzzle['question'], $position);
            $word = explode(' ', $paragraphs[(int) $image[1] - 1][(int) $position[1] - 1])[(int) $position[2] - 1];
            $this->assertSame($challenge->normalize($word), $puzzle['answer']);
        }
    }

    public function test_correct_answer_unlocks_original_url_and_cannot_be_replayed(): void
    {
        $this->get('/gate-test?read=1')->assertRedirect('/human-check');
        $this->get('/human-check');
        $puzzle = session('human_check.puzzle');
        $payload = ['challenge_id' => $puzzle['id'], 'answer' => $puzzle['answer']];
        $this->post('/human-check', $payload)->assertRedirect('/gate-test?read=1');
        $this->assertNotNull(session('human_check.verified'));
        $this->get('/gate-test')->assertOk();
        $this->post('/human-check', $payload)->assertSessionHas('challenge_error');
        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.10'])->get('/gate-test')->assertRedirect('/human-check');
    }

    public function test_wrong_expired_and_stale_answers_do_not_unlock(): void
    {
        $this->get('/human-check');
        $puzzle = session('human_check.puzzle');
        $this->post('/human-check', ['challenge_id' => $puzzle['id'], 'answer' => 'wrong'])->assertSessionHas('challenge_error');
        $this->assertNull(session('human_check.verified'));
        $puzzle['expires'] = time() - 1;
        $this->withSession(['human_check.puzzle' => $puzzle])
            ->post('/human-check', ['challenge_id' => $puzzle['id'], 'answer' => $puzzle['answer']])->assertSessionHas('challenge_error');
        $this->assertNull(session('human_check.verified'));
        $this->get('/human-check');
        $old = session('human_check.puzzle');
        $this->get('/human-check');
        $this->post('/human-check', ['challenge_id' => $old['id'], 'answer' => $old['answer']])->assertSessionHas('challenge_error');
    }

    public function test_attempts_are_limited(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->post('/human-check', ['answer' => 'wrong'])->assertRedirect('/human-check');
        }
        $this->post('/human-check', ['answer' => 'wrong'])->assertStatus(429);
    }

    public function test_untrusted_headers_cannot_spoof_localhost(): void
    {
        config(['human-check.trusted_proxies' => ['192.0.2.1']]);
        $request = Request::create('/', 'GET', [], [], [], [
            'REMOTE_ADDR' => '198.51.100.9', 'HTTP_CF_CONNECTING_IP' => '127.0.0.1',
            'HTTP_X_FORWARDED_FOR' => '127.0.0.1',
        ]);
        $this->assertSame('198.51.100.9', (new VisitorCountry)->ip($request));
        $request->server->set('REMOTE_ADDR', '192.0.2.1');
        $request->headers->set('CF-Connecting-IP', '203.0.113.10');
        $this->assertSame('203.0.113.10', (new VisitorCountry)->ip($request));
    }

    public function test_turkish_case_and_punctuation_normalization(): void
    {
        $this->assertSame('ışık', (new HumanChallenge)->normalize(' IŞIK. '));
        $this->assertSame('izmir', (new HumanChallenge)->normalize('İZMİR'));
    }
}
