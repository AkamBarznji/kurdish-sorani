import 'package:kurdish_sorani/kurdish_sorani.dart';

void main() {
  print('كوردستان'.kurdishNormalize()); // کوردستان
  print(25000.toIqd()); // ٢٥٬٠٠٠ د.ع
  print(2026.soraniWords); // دوو هەزار و بیست و شەش

  final phone = IraqiPhone.tryParse('0750 123 4567');
  print(phone?.e164); // +9647501234567
  print(phone?.carrier); // Korek
}
