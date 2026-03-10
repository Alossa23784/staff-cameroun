<?php
declare(strict_types=1);

namespace Staff\Services;

class MonetbilService
{
    private string $serviceKey;
    private string $serviceSecret;
    private string $widgetUrl = 'https://api.monetbil.com/widget/v2.1/';

    public function __construct()
    {
        $this->serviceKey    = MONETBIL_SERVICE_KEY;
        $this->serviceSecret = MONETBIL_SERVICE_SECRET;
    }

    public function genererUrlPaiement(int $userId, float $montant, string $reference, string $email='', string $telephone='', string $prenom='', string $nom=''): string
    {
        $params = [
            'amount'     => (int)$montant,
            'phone'      => $telephone,
            'locale'     => 'fr',
            'operator'   => '',
            'reference'  => $reference,
            'user'       => (string)$userId,
            'first_name' => $prenom,
            'last_name'  => $nom,
            'email'      => $email,
            'return_url' => MONETBIL_RETURN_URL,
            'notify_url' => MONETBIL_NOTIFY_URL,
        ];
        return $this->widgetUrl . $this->serviceKey . '?' . http_build_query($params);
    }

    public function verifierSignature(array $data): bool
    {
        $sign = $data['sign'] ?? '';
        unset($data['sign']);
        ksort($data);
        $str      = implode('', array_values($data));
        $expected = hash_hmac('sha256', $str, $this->serviceSecret);
        return hash_equals($expected, $sign);
    }

    public function verifierPaiement(string $paymentRef): array
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => 'https://api.monetbil.com/payment/v1/checkPayment',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query(['serviceKey' => $this->serviceKey, 'paymentRef' => $paymentRef]),
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        $data   = json_decode($response ?: '{}', true) ?? [];
        $statut = match(strtoupper($data['status'] ?? '')) {
            'SUCCESS' => 'succes', 'FAILED' => 'echec', default => 'en_attente',
        };
        return ['success' => $statut === 'succes', 'statut' => $statut, 'raw' => $data];
    }
}
