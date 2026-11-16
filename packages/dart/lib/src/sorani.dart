/// Kurdish (Sorani) text utilities: normalization, digits, IQD formatting
/// and number-to-words.
abstract final class Sorani {
  static const _zwnj = '‌';

  static const _kurdishDigits = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
  static const _persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

  static const _ones = ['', 'یەک', 'دوو', 'سێ', 'چوار', 'پێنج', 'شەش', 'حەوت', 'هەشت', 'نۆ'];
  static const _teens = ['دە', 'یازدە', 'دوازدە', 'سیازدە', 'چواردە', 'پازدە', 'شازدە', 'حەڤدە', 'هەژدە', 'نۆزدە'];
  static const _tens = ['', '', 'بیست', 'سی', 'چل', 'پەنجا', 'شەست', 'حەفتا', 'هەشتا', 'نەوەد'];
  static const _scales = ['', 'هەزار', 'ملیۆن', 'ملیار', 'ترلیۆن'];

  static final _finalHeh = RegExp(r'ه(?!\p{L})', unicode: true);

  /// Converts Arabic-keyboard characters to their correct Kurdish forms:
  /// ك→ک, ي/ى→ی, ة→ە, ه+ZWNJ→ە, and removes tatweel (ـ).
  ///
  /// With [finalHeh] set, a word-final ه is also converted to ە.
  static String normalize(String text, {bool finalHeh = false}) {
    var s = text
        .replaceAll('ـ', '')
        .replaceAll('ك', 'ک')
        .replaceAll('ي', 'ی')
        .replaceAll('ى', 'ی')
        .replaceAll('ة', 'ە')
        .replaceAll('ه$_zwnj', 'ە');
    if (finalHeh) {
      s = s.replaceAll(_finalHeh, 'ە');
    }
    return s;
  }

  /// Converts Latin (0-9) and Persian (۰-۹) digits to Kurdish digits (٠-٩).
  static String toKurdishDigits(String text) {
    final b = StringBuffer();
    for (final ch in text.split('')) {
      final latin = ch.codeUnitAt(0) - 0x30;
      if (latin >= 0 && latin <= 9) {
        b.write(_kurdishDigits[latin]);
        continue;
      }
      final p = _persianDigits.indexOf(ch);
      b.write(p >= 0 ? _kurdishDigits[p] : ch);
    }
    return b.toString();
  }

  /// Converts Kurdish (٠-٩) and Persian (۰-۹) digits to Latin digits (0-9).
  static String toLatinDigits(String text) {
    final b = StringBuffer();
    for (final ch in text.split('')) {
      var i = _kurdishDigits.indexOf(ch);
      if (i < 0) i = _persianDigits.indexOf(ch);
      b.write(i >= 0 ? '$i' : ch);
    }
    return b.toString();
  }

  /// Formats an amount in Iraqi Dinar, e.g. `25000` → `٢٥٬٠٠٠ د.ع`.
  static String formatIqd(num amount, {bool kurdishDigits = true, String suffix = 'د.ع'}) {
    final value = amount.round();
    final digits = value.abs().toString();
    final sep = kurdishDigits ? '٬' : ',';

    final b = StringBuffer();
    for (var i = 0; i < digits.length; i++) {
      if (i > 0 && (digits.length - i) % 3 == 0) b.write(sep);
      b.write(digits[i]);
    }

    var formatted = b.toString();
    if (kurdishDigits) formatted = toKurdishDigits(formatted);
    final sign = value < 0 ? '-' : '';
    return suffix.isEmpty ? '$sign$formatted' : '$sign$formatted $suffix';
  }

  /// Spells out a non-negative integer in Sorani,
  /// e.g. `2026` → `دوو هەزار و بیست و شەش`.
  static String numberToWords(int number) {
    if (number < 0) {
      throw ArgumentError.value(number, 'number', 'Negative numbers are not supported.');
    }
    if (number == 0) return 'سفر';

    final groups = <int>[];
    while (number > 0) {
      groups.add(number % 1000);
      number ~/= 1000;
    }
    if (groups.length > _scales.length) {
      throw ArgumentError('Number is too large.');
    }

    final parts = <String>[];
    for (var i = groups.length - 1; i >= 0; i--) {
      final g = groups[i];
      if (g == 0) continue;
      if (i == 0) {
        parts.add(_belowThousand(g));
      } else if (i == 1 && g == 1) {
        parts.add(_scales[1]);
      } else {
        parts.add('${_belowThousand(g)} ${_scales[i]}');
      }
    }
    return parts.join(' و ');
  }

  static String _belowThousand(int n) {
    final parts = <String>[];
    final hundreds = n ~/ 100;
    final rest = n % 100;

    if (hundreds > 0) {
      parts.add('${hundreds == 1 ? '' : _ones[hundreds]}سەد');
    }
    if (rest >= 10 && rest < 20) {
      parts.add(_teens[rest - 10]);
    } else if (rest > 0) {
      final t = rest ~/ 10;
      final o = rest % 10;
      if (t > 0) parts.add(_tens[t]);
      if (o > 0) parts.add(_ones[o]);
    }
    return parts.join(' و ');
  }
}

/// Convenience extensions on [String].
extension SoraniString on String {
  /// See [Sorani.normalize].
  String kurdishNormalize({bool finalHeh = false}) => Sorani.normalize(this, finalHeh: finalHeh);

  /// See [Sorani.toKurdishDigits].
  String get kurdishDigits => Sorani.toKurdishDigits(this);

  /// See [Sorani.toLatinDigits].
  String get latinDigits => Sorani.toLatinDigits(this);
}

/// Convenience extensions on [int].
extension SoraniInt on int {
  /// See [Sorani.numberToWords].
  String get soraniWords => Sorani.numberToWords(this);

  /// See [Sorani.formatIqd].
  String toIqd({bool kurdishDigits = true}) => Sorani.formatIqd(this, kurdishDigits: kurdishDigits);
}
