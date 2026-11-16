import 'sorani.dart';

/// An Iraqi mobile number, parsed from almost any user input:
/// `0750 123 4567`, `+964 750 123 4567`, `00964-750-1234567`, `٠٧٥٠١٢٣٤٥٦٧` ...
final class IraqiPhone {
  const IraqiPhone._(this._digits);

  /// 10-digit subscriber number without the leading 0, e.g. `7501234567`.
  final String _digits;

  static const _carriers = {
    '75': 'Korek',
    '77': 'Asiacell',
    '78': 'Zain',
    '79': 'Zain',
  };

  static final _strip = RegExp(r'[\s\-().]');
  static final _valid = RegExp(r'^7[3-9]\d{8}$');

  /// Parses [input]; returns `null` when it is not a valid Iraqi mobile number.
  static IraqiPhone? tryParse(String? input) {
    if (input == null) return null;

    var s = Sorani.toLatinDigits(input.trim()).replaceAll(_strip, '');

    if (s.startsWith('+964')) {
      s = s.substring(4);
    } else if (s.startsWith('00964')) {
      s = s.substring(5);
    } else if (s.startsWith('964') && s.length == 13) {
      s = s.substring(3);
    }
    if (s.startsWith('0')) s = s.substring(1);

    return _valid.hasMatch(s) ? IraqiPhone._(s) : null;
  }

  /// Whether [input] is a valid Iraqi mobile number.
  static bool isValid(String? input) => tryParse(input) != null;

  /// A ready-made validator for `TextFormField.validator`.
  static String? Function(String?) validator({
    String message = 'ژمارەی مۆبایل دروست نییە',
  }) =>
      (value) => isValid(value) ? null : message;

  /// E.164 format, e.g. `+9647501234567` — store this in your database.
  String get e164 => '+964$_digits';

  /// Local display format, e.g. `0750 123 4567`.
  String get national =>
      '0${_digits.substring(0, 3)} ${_digits.substring(3, 6)} ${_digits.substring(6)}';

  /// `Korek`, `Asiacell`, `Zain`, or `null` when unknown.
  String? get carrier => _carriers[_digits.substring(0, 2)];

  @override
  String toString() => e164;

  @override
  bool operator ==(Object other) => other is IraqiPhone && other._digits == _digits;

  @override
  int get hashCode => _digits.hashCode;
}
