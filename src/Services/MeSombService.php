<?php
declare(strict_types=1);

namespace Staff\Services;

/**
 * MeSomb Payment Service
 * Gère les collectes MTN MoMo et Orange Money via MeSomb API v1.1
 */
class MeSombService
{
    private string $appKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->appKey  = MESOMB_APP_KEY;
        $this->baseUrl = MESOMB_BASE_URL;
    }

    /**
     * Initier une collecte (demande de paiement)
     *
     * @param string $operateur  'MTN' | 'ORANGE'
     * @param string $telephone  Numéro ex: 650000000
     * @param float  $montant    Montant en FCFA
     * @param string $reference  Référence unique (ex: STAFF-42-1710000000)
     * @return array ['success'=>bool, 'transaction_id'=>string|null, 'message'=>string]
     */
    public function collecter(
        string $operateur,
        string $telephone,
        float  $montant,
        string $reference
    ): array {
        $payload = [
            'amount'    => (int)$montant,
            'service'   => strtoupper($operateur),
            'payer'     => $this->normaliserTelephone($telephone),
            'currency'  => 'XAF',
            'reference' => $reference,
        ];

        $response = $this->request('POST', '/payment/collect/', $payload);

        if ($response['http_code'] === 200 || $response['http_code'] === 201) {
            $data = $response['body'];
            return [
                'success'        => true,
                'transaction_id' => $data['pk']           ?? null,
                'status'         => $data['status']       ?? 'SUCCESS',
                'message'        => 'Paiement initié avec succès',
                'raw'            => $data,
            ];
        }

        return [
            'success'        => false,
            'transaction_id' => null,
            'status'         => 'FAILED',
            'message'        => $response['body']['detail'] ?? 'Erreur MeSomb inconnue',
            'raw'            => $response['body'],
        ];
    }

    /**
     * Vérifier le statut d'une transaction
     */
    public function verifierStatut(string $transactionId): array
    {
        $response = $this->request('GET', "/payment/status/{$transactionId}/");

        if ($response['http_code'] === 200) {
            $data   = $response['body'];
            $statut = match(strtoupper($data['status'] ?? '')) {
                'SUCCESS' => 'succes',
                'FAILED'  => 'echec',
                default   => 'en_attente',
            };
            return ['success' => true, 'statut' => $statut, 'raw' => $data];
        }

        return ['success' => false, 'statut' => 'en_attente', 'raw' => $response['body']];
    }

    // ── Requête HTTP cURL ────────────────────────────────
    private function request(string $method, string $endpoint, array $body = []): array
    {
        $url = $this->baseUrl . $endpoint;
        $ch  = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'X-MeSomb-Application: ' . $this->appKey,
                'Authorization: Application '  . $this->appKey,
            ],
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }

        $raw      = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'http_code' => $httpCode,
            'body'      => json_decode($raw ?: '{}', true) ?? [],
        ];
    }

    /** Normalise le numéro : supprime +237 ou 237 en tête */
    private function normaliserTelephone(string $tel): string
    {
        $tel = preg_replace('/\D/', '', $tel);
        if (str_starts_with($tel, '237')) {
            $tel = substr($tel, 3);
        }
        return $tel;
    }
}
