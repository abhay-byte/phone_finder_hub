<?php

namespace App\Services\Firestore;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Firestore REST API Client
 * Works without the grpc PHP extension
 */
class FirestoreClient
{
    protected string $projectId;

    protected string $baseUrl;

    protected ?string $accessToken = null;

    protected ?int $tokenExpires = null;

    public function __construct()
    {
        $this->projectId = config('services.firebase.project_id', config('firebase.projects.app.credentials.project_id'));
        $this->baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents";
    }

    /**
     * Get OAuth 2.0 access token for service account
     */
    protected function getAccessToken(): string
    {
        if ($this->accessToken && $this->tokenExpires && time() < $this->tokenExpires - 60) {
            return $this->accessToken;
        }

        $credentialsPath = config('firebase.projects.app.credentials');

        if (! $credentialsPath || ! file_exists($credentialsPath)) {
            throw new \RuntimeException('Firebase service account credentials not found at: '.$credentialsPath);
        }

        $credentials = json_decode(file_get_contents($credentialsPath), true);

        $now = time();
        $jwtHeader = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
        $jwtClaim = json_encode([
            'iss' => $credentials['client_email'],
            'sub' => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/datastore',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
        ]);

        $jwtHeaderB64 = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($jwtHeader));
        $jwtClaimB64 = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($jwtClaim));
        $signatureInput = $jwtHeaderB64.'.'.$jwtClaimB64;

        $privateKey = openssl_pkey_get_private($credentials['private_key']);
        openssl_sign($signatureInput, $signature, $privateKey, 'sha256');
        $signatureB64 = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        $jwt = $signatureInput.'.'.$signatureB64;

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException('Failed to get Firestore access token: '.$response->body());
        }

        $data = $response->json();
        $this->accessToken = $data['access_token'];
        $this->tokenExpires = $now + $data['expires_in'];

        return $this->accessToken;
    }

    /**
     * Make an authenticated request to Firestore API
     */
    protected function request(string $method, string $url, array $data = []): ?array
    {
        try {
            $token = $this->getAccessToken();
            $response = Http::withToken($token, 'Bearer')
                ->timeout(30)
                ->{$method}($url, $data);

            if (! $response->successful()) {
                Log::error("Firestore API error: {$response->status()} - {$response->body()}");

                return null;
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error("Firestore request failed: {$e->getMessage()}");

            return null;
        }
    }

    /**
     * Convert PHP value to Firestore Value object
     */
    protected function toFirestoreValue(mixed $value): array
    {
        if ($value === null) {
            return ['nullValue' => null];
        }
        if (is_bool($value)) {
            return ['booleanValue' => $value];
        }
        if (is_int($value)) {
            return ['integerValue' => (string) $value];
        }
        if (is_float($value)) {
            return ['doubleValue' => $value];
        }
        if (is_array($value)) {
            // Check if associative array (map) or indexed array
            if (array_keys($value) !== range(0, count($value) - 1)) {
                $fields = [];
                foreach ($value as $k => $v) {
                    $fields[$k] = $this->toFirestoreValue($v);
                }

                return ['mapValue' => ['fields' => $fields]];
            }
            $values = [];
            foreach ($value as $v) {
                $values[] = $this->toFirestoreValue($v);
            }

            return ['arrayValue' => ['values' => $values]];
        }
        if ($value instanceof \DateTimeInterface) {
            return ['timestampValue' => $value->format('c')];
        }

        return ['stringValue' => (string) $value];
    }

    /**
     * Convert Firestore Value object to PHP value
     */
    protected function fromFirestoreValue(array $value): mixed
    {
        if (isset($value['nullValue'])) {
            return null;
        }
        if (isset($value['booleanValue'])) {
            return $value['booleanValue'];
        }
        if (isset($value['integerValue'])) {
            return (int) $value['integerValue'];
        }
        if (isset($value['doubleValue'])) {
            return $value['doubleValue'];
        }
        if (isset($value['stringValue'])) {
            return $value['stringValue'];
        }
        if (isset($value['timestampValue'])) {
            return new \DateTime($value['timestampValue']);
        }
        if (isset($value['arrayValue']['values'])) {
            return array_map(fn ($v) => $this->fromFirestoreValue($v), $value['arrayValue']['values']);
        }
        if (isset($value['mapValue']['fields'])) {
            $result = [];
            foreach ($value['mapValue']['fields'] as $k => $v) {
                $result[$k] = $this->fromFirestoreValue($v);
            }

            return $result;
        }
        if (isset($value['referenceValue'])) {
            return $value['referenceValue'];
        }

        return null;
    }

    /**
     * Get a document by path
     */
    public function getDocument(string $collection, string $documentId): ?array
    {
        $url = "{$this->baseUrl}/{$collection}/{$documentId}";
        $result = $this->request('get', $url);

        if (! $result || ! isset($result['fields'])) {
            return null;
        }

        $data = ['id' => $documentId];
        foreach ($result['fields'] as $key => $value) {
            $data[$key] = $this->fromFirestoreValue($value);
        }

        return $data;
    }

    /**
     * Create or update a document
     */
    public function setDocument(string $collection, string $documentId, array $data, bool $merge = false): bool
    {
        $url = "{$this->baseUrl}/{$collection}/{$documentId}";
        $fields = [];
        foreach ($data as $key => $value) {
            $fields[$key] = $this->toFirestoreValue($value);
        }

        $payload = ['fields' => $fields];
        $queryParams = $merge ? '?updateMask.fieldPaths='.implode('&updateMask.fieldPaths=', array_keys($data)) : '';

        $result = $this->request('patch', $url.$queryParams, $payload);

        return $result !== null;
    }

    /**
     * Delete a document
     */
    public function deleteDocument(string $collection, string $documentId): bool
    {
        $url = "{$this->baseUrl}/{$collection}/{$documentId}";
        $result = $this->request('delete', $url);

        return $result !== null || true; // DELETE returns empty body on success
    }

    /**
     * Query documents in a collection
     */
    public function query(string $collection, array $filters = [], array $orderBy = [], int $limit = 0): array
    {
        $url = "{$this->baseUrl}:runQuery";

        $structuredQuery = [
            'from' => [['collectionId' => $collection]],
        ];

        if (! empty($filters)) {
            $filterClauses = [];
            foreach ($filters as $field => $condition) {
                $op = $condition['op'] ?? 'EQUAL';
                $value = $this->toFirestoreValue($condition['value']);
                $filterClauses[] = [
                    'fieldFilter' => [
                        'field' => ['fieldPath' => $field],
                        'op' => $op,
                        'value' => $value,
                    ],
                ];
            }
            if (count($filterClauses) === 1) {
                $structuredQuery['where'] = $filterClauses[0];
            } else {
                $structuredQuery['where'] = ['compositeFilter' => ['op' => 'AND', 'filters' => $filterClauses]];
            }
        }

        if (! empty($orderBy)) {
            $structuredQuery['orderBy'] = [];
            foreach ($orderBy as $field => $direction) {
                $structuredQuery['orderBy'][] = [
                    'field' => ['fieldPath' => $field],
                    'direction' => $direction === 'desc' ? 'DESCENDING' : 'ASCENDING',
                ];
            }
        }

        if ($limit > 0) {
            $structuredQuery['limit'] = $limit;
        }

        $payload = ['structuredQuery' => $structuredQuery];
        $result = $this->request('post', $url, $payload);

        if (! $result) {
            return [];
        }

        $documents = [];
        foreach ($result as $doc) {
            if (! isset($doc['document'])) {
                continue;
            }
            $docData = $doc['document'];
            $id = basename($docData['name']);
            $item = ['id' => $id];
            if (isset($docData['fields'])) {
                foreach ($docData['fields'] as $key => $value) {
                    $item[$key] = $this->fromFirestoreValue($value);
                }
            }
            $documents[] = $item;
        }

        return $documents;
    }

    /**
     * List all documents in a collection
     */
    public function listDocuments(string $collection): array
    {
        $url = "{$this->baseUrl}/{$collection}";
        $result = $this->request('get', $url);

        if (! $result || ! isset($result['documents'])) {
            return [];
        }

        $documents = [];
        foreach ($result['documents'] as $doc) {
            $id = basename($doc['name']);
            $item = ['id' => $id];
            if (isset($doc['fields'])) {
                foreach ($doc['fields'] as $key => $value) {
                    $item[$key] = $this->fromFirestoreValue($value);
                }
            }
            $documents[] = $item;
        }

        return $documents;
    }
}
