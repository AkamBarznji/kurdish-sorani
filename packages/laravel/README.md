# universeitgroup/kurdish-sorani

Kurdish (Sorani) toolkit for Laravel 10, 11 and 12.

```bash
composer require universeitgroup/kurdish-sorani
```

The service provider registers itself automatically.

## Kurdish translations

```bash
php artisan vendor:publish --tag=kurdish-sorani-lang
```

This copies `lang/ckb/` (validation, auth, passwords, pagination) into your app. Then set `APP_LOCALE=ckb`.

## Iraqi phone validation

```php
use UniverseIT\KurdishSorani\Rules\IraqiPhone;

$request->validate([
    'phone' => ['required', new IraqiPhone()],
    // or: 'phone' => 'required|iraqi_phone',
]);
```

```php
use UniverseIT\KurdishSorani\IraqiPhone;

$phone = IraqiPhone::parse('٠٧٥٠ ١٢٣ ٤٥٦٧');
$phone->e164();     // +9647501234567  (store this)
$phone->national(); // 0750 123 4567
$phone->carrier();  // Korek
```

## Normalize all Kurdish input

Add the middleware so `كوردستان` and `کوردستان` are stored the same way (password fields are skipped):

```php
// bootstrap/app.php (Laravel 11+)
->withMiddleware(function (Middleware $middleware) {
    $middleware->append(\UniverseIT\KurdishSorani\Http\Middleware\NormalizeKurdishInput::class);
})
```

## Helpers

```php
use Illuminate\Support\Str;
use UniverseIT\KurdishSorani\Sorani;

Str::kurdishNormalize('سلێماني'); // سلێمانی
Str::kurdishDigits('2026');       // ٢٠٢٦
Str::latinDigits('٢٠٢٦');         // 2026
Str::soraniWords(1500);           // هەزار و پێنجسەد

Sorani::formatIqd(25000);                       // ٢٥٬٠٠٠ د.ع
Sorani::formatIqd(25000, kurdishDigits: false); // 25,000 د.ع
```

Blade:

```blade
<span>@iqd($product->price)</span>
```

The core classes (`Sorani`, `IraqiPhone`) have no Laravel dependency and work in any PHP 8.1+ project.

## License

MIT © Universe IT Group
