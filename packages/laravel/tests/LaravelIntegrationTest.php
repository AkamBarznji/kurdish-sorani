<?php

declare(strict_types=1);

namespace UniverseIT\KurdishSorani\Tests;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase;
use UniverseIT\KurdishSorani\Http\Middleware\NormalizeKurdishInput;
use UniverseIT\KurdishSorani\KurdishSoraniServiceProvider;
use UniverseIT\KurdishSorani\Rules\IraqiPhone;

final class LaravelIntegrationTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [KurdishSoraniServiceProvider::class];
    }

    public function test_rule_object(): void
    {
        $this->assertTrue(Validator::make(['phone' => '0750 123 4567'], ['phone' => [new IraqiPhone()]])->passes());

        $v = Validator::make(['phone' => '123'], ['phone' => [new IraqiPhone()]]);
        $this->assertTrue($v->fails());
        $this->assertSame('The phone field must be a valid Iraqi mobile number.', $v->errors()->first('phone'));
    }

    public function test_string_rule_and_kurdish_message(): void
    {
        $this->assertTrue(Validator::make(['phone' => '+9647701234567'], ['phone' => 'iraqi_phone'])->passes());

        app()->setLocale('ckb');
        $v = Validator::make(['phone' => 'abc'], ['phone' => 'iraqi_phone']);
        $this->assertTrue($v->fails());
        $this->assertSame('خانەی phone دەبێت ژمارەی مۆبایلێکی دروستی عێراق بێت.', $v->errors()->first('phone'));
    }

    public function test_str_macros(): void
    {
        $this->assertSame('کوردستان', Str::kurdishNormalize('كوردستان'));
        $this->assertSame('٢٠٢٦', Str::kurdishDigits('2026'));
        $this->assertSame('2026', Str::latinDigits('٢٠٢٦'));
        $this->assertSame('دوو هەزار و بیست و شەش', Str::soraniWords(2026));
    }

    public function test_blade_directive(): void
    {
        $this->assertSame('٢٥٬٠٠٠ د.ع', trim(Blade::render('@iqd($price)', ['price' => 25000])));
    }

    public function test_middleware_normalizes_but_skips_password(): void
    {
        $request = Request::create('/', 'POST', ['title' => 'كوردستان', 'password' => 'كي']);
        (new NormalizeKurdishInput())->handle($request, function (Request $r) {
            $this->assertSame('کوردستان', $r->input('title'));
            $this->assertSame('كي', $r->input('password'));

            return response('ok');
        });
    }
}
