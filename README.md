# Kurdish Sorani Toolkit

**Kurdish (Sorani / ckb) support for Flutter and Laravel** — the small things every Kurdish app needs and every developer rewrites from scratch.

| Package | Install |
|---|---|
| [`kurdish_sorani`](packages/dart) (Dart / Flutter) | `flutter pub add kurdish_sorani` |
| [`universeitgroup/kurdish-sorani`](packages/laravel) (Laravel) | `composer require universeitgroup/kurdish-sorani` |

## Why

Most Kurdish users type with an Arabic or Persian keyboard, so the same word gets stored in different ways (`كوردستان` vs `کوردستان`, `سلێماني` vs `سلێمانی`). Search breaks, duplicate checks fail, and text looks wrong. On top of that, every app in Kurdistan needs Iraqi phone validation, Dinar formatting and Kurdish digits.

## Features (both packages)

- **Text normalization** — ك→ک, ي/ى→ی, ة→ە, ه‌→ە, removes tatweel
- **Digits** — `2026` ↔ `٢٠٢٦` (also handles Persian ۰-۹)
- **IQD formatting** — `25000` → `٢٥٬٠٠٠ د.ع`
- **Number to words** — `2026` → `دوو هەزار و بیست و شەش` (for invoices and receipts)
- **Iraqi mobile numbers** — parse any format, validate, get E.164 and carrier (Korek / Asiacell / Zain)

Laravel extras: `iraqi_phone` validation rule, input-normalizing middleware, `@iqd` Blade directive, `Str` macros, and **full Sorani translations** for Laravel's validation, auth, password and pagination messages.

## Consistency

Both packages run the same [test vectors](test-vectors/vectors.json) in CI, so Flutter and Laravel always produce identical output.

---

## بە کوردی

ئامرازێکی کراوە بۆ پشتگیریکردنی زمانی کوردی (سۆرانی) لە Flutter و Laravel:

- **ڕاستکردنەوەی پیتەکان:** ك→ک، ي→ی، ة→ە، تاکو گەڕان و بەراوردکردن دروست کار بکەن
- **ژمارەی کوردی:** ‎2026 ↔ ٢٠٢٦
- **دینار:** ‎25000 → ٢٥٬٠٠٠ د.ع
- **ژمارە بە نووسین:** ‎2026 → دوو هەزار و بیست و شەش
- **ژمارەی مۆبایلی عێراق:** پشکنین، ڕێکخستن و ناسینەوەی کۆمپانیا (کۆڕەک، ئاسیاسێڵ، زەین)

بەشداربوون بەخێربێن: ئەگەر هەڵەیەکی زمانیت بینی یان تایبەتمەندییەکی نوێت دەوێت، issue یان pull request بکەرەوە.

## License

MIT © Universe IT Group
