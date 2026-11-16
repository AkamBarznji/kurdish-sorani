<?php

declare(strict_types=1);

namespace UniverseIT\KurdishSorani;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

final class KurdishSoraniServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../lang/package', 'kurdish-sorani');

        if ($this->app->runningInConsole()) {
            // php artisan vendor:publish --tag=kurdish-sorani-lang
            $this->publishes([
                __DIR__ . '/../lang/ckb' => $this->app->langPath('ckb'),
            ], 'kurdish-sorani-lang');
        }

        Validator::extend('iraqi_phone', static fn ($attribute, $value): bool => is_string($value) && IraqiPhone::isValid($value));
        Validator::replacer('iraqi_phone', static fn ($message, $attribute, $rule, $parameters, $validator) => $message === 'validation.iraqi_phone'
            ? trans('kurdish-sorani::validation.iraqi_phone', ['attribute' => $validator->getDisplayableAttribute($attribute)])
            : $message);

        Str::macro('kurdishNormalize', static fn (string $text, bool $finalHeh = false): string => Sorani::normalize($text, $finalHeh));
        Str::macro('kurdishDigits', static fn (string $text): string => Sorani::toKurdishDigits($text));
        Str::macro('latinDigits', static fn (string $text): string => Sorani::toLatinDigits($text));
        Str::macro('soraniWords', static fn (int $number): string => Sorani::numberToWords($number));

        // @iqd($price)  →  ٢٥٬٠٠٠ د.ع
        Blade::directive('iqd', static fn (string $expression): string => "<?php echo e(\\UniverseIT\\KurdishSorani\\Sorani::formatIqd({$expression})); ?>");
    }
}
