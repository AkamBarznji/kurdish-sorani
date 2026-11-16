# kurdish_sorani

Kurdish (Sorani) toolkit for Dart and Flutter. Pure Dart, no dependencies.

```dart
import 'package:kurdish_sorani/kurdish_sorani.dart';

'كوردستان'.kurdishNormalize();    // کوردستان
'2026'.kurdishDigits;             // ٢٠٢٦
'٠٧٥٠'.latinDigits;               // 0750
25000.toIqd();                    // ٢٥٬٠٠٠ د.ع
2026.soraniWords;                 // دوو هەزار و بیست و شەش

final phone = IraqiPhone.tryParse('0750 123 4567');
phone?.e164;      // +9647501234567
phone?.national;  // 0750 123 4567
phone?.carrier;   // Korek
```

### Form validation

```dart
TextFormField(
  keyboardType: TextInputType.phone,
  validator: IraqiPhone.validator(),
)
```

### Normalizing text

`Sorani.normalize` converts characters typed on Arabic/Persian keyboards to the correct Kurdish Unicode:
ك→ک, ي/ى→ی, ة→ە, ه + ZWNJ→ە, and removes tatweel (ـ).

Pass `finalHeh: true` to also turn a word-final ه into ە. It is off by default because a few words legitimately end in ه.

Normalize text **before saving and before searching** so `كوردستان` and `کوردستان` match.

### بە کوردی

پاکێجێکی Dart/Flutter بۆ زمانی کوردی: ڕاستکردنەوەی پیتەکان، ژمارەی کوردی، ڕێکخستنی دینار، ژمارە بە نووسین و پشکنینی ژمارەی مۆبایلی عێراق.

## License

MIT © Universe IT Group
