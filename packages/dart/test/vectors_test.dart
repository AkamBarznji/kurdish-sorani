import 'dart:convert';
import 'dart:io';

import 'package:kurdish_sorani/kurdish_sorani.dart';
import 'package:test/test.dart';

/// Runs the shared vectors (../../test-vectors/vectors.json) that the Laravel
/// package also runs, so both packages always behave the same.
void main() {
  final v = jsonDecode(File('../../test-vectors/vectors.json').readAsStringSync()) as Map<String, dynamic>;
  List<Map<String, dynamic>> cases(String key) => (v[key] as List).cast<Map<String, dynamic>>();

  test('normalize', () {
    for (final c in cases('normalize')) {
      expect(Sorani.normalize(c['in'] as String), c['out'], reason: c['in'] as String);
    }
    for (final c in cases('normalizeFinalHeh')) {
      expect(Sorani.normalize(c['in'] as String, finalHeh: true), c['out'], reason: c['in'] as String);
    }
  });

  test('digits', () {
    for (final c in cases('toKurdishDigits')) {
      expect(Sorani.toKurdishDigits(c['in'] as String), c['out']);
    }
    for (final c in cases('toLatinDigits')) {
      expect(Sorani.toLatinDigits(c['in'] as String), c['out']);
    }
  });

  test('formatIqd', () {
    for (final c in cases('formatIqd')) {
      expect(Sorani.formatIqd(c['amount'] as int, kurdishDigits: c['kurdishDigits'] as bool), c['out']);
    }
  });

  test('numberToWords', () {
    for (final c in cases('numberToWords')) {
      expect(Sorani.numberToWords(c['in'] as int), c['out'], reason: '${c['in']}');
    }
    expect(() => Sorani.numberToWords(-1), throwsArgumentError);
  });

  test('valid phones', () {
    for (final c in cases('phoneValid')) {
      final p = IraqiPhone.tryParse(c['in'] as String);
      expect(p, isNotNull, reason: c['in'] as String);
      expect(p!.e164, c['e164']);
      expect(p.national, c['national']);
      expect(p.carrier, c['carrier']);
    }
  });

  test('invalid phones', () {
    for (final input in (v['phoneInvalid'] as List).cast<String>()) {
      expect(IraqiPhone.isValid(input), isFalse, reason: input);
    }
    expect(IraqiPhone.isValid(null), isFalse);
  });

  test('extensions and form validator', () {
    expect('كوردستان'.kurdishNormalize(), 'کوردستان');
    expect('2026'.kurdishDigits, '٢٠٢٦');
    expect('٢٠٢٦'.latinDigits, '2026');
    expect(2026.soraniWords, 'دوو هەزار و بیست و شەش');
    expect(25000.toIqd(), '٢٥٬٠٠٠ د.ع');

    final validate = IraqiPhone.validator();
    expect(validate('0750 123 4567'), isNull);
    expect(validate('123'), 'ژمارەی مۆبایل دروست نییە');
  });
}
