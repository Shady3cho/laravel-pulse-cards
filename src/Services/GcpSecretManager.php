<?php

namespace LangtonMwanza\PulseDevops\Services;

use Exception;
use Google\Cloud\SecretManager\V1\Client\SecretManagerServiceClient;
use Google\Cloud\SecretManager\V1\AccessSecretVersionRequest;
use Google\Cloud\SecretManager\V1\AddSecretVersionRequest;
use Google\Cloud\SecretManager\V1\CreateSecretRequest;
use Google\Cloud\SecretManager\V1\ListSecretsRequest;
use Google\Cloud\SecretManager\V1\Replication;
use Google\Cloud\SecretManager\V1\Replication\Automatic;
use Google\Cloud\SecretManager\V1\Secret;
use Google\Cloud\SecretManager\V1\SecretPayload;

class GcpSecretManager
{
    protected ?SecretManagerServiceClient $client = null;

    protected ?string $projectId;

    public function __construct()
    {
        $this->projectId = config('pulse-devops.gcp.project_id');
    }

    protected function getClient(): SecretManagerServiceClient
    {
        if ($this->client === null) {
            $this->client = new SecretManagerServiceClient();
        }

        return $this->client;
    }

    public function isConfigured(): bool
    {
        if (empty($this->projectId)) {
            return false;
        }

        try {
            $this->getClient();

            return true;
        } catch (Exception) {
            return false;
        }
    }

    public function listSecrets(): array
    {
        $parent = SecretManagerServiceClient::projectName($this->projectId);

        $request = (new ListSecretsRequest())->setParent($parent);
        $response = $this->getClient()->listSecrets($request);

        $secrets = [];
        foreach ($response->iterateAllElements() as $secret) {
            $name = $this->extractSecretName($secret->getName());
            $secrets[] = [
                'name' => $name,
                'full_name' => $secret->getName(),
                'created' => $secret->getCreateTime()?->toDateTime()?->format('Y-m-d H:i:s'),
                'labels' => iterator_to_array($secret->getLabels()),
            ];
        }

        return $secrets;
    }

    public function getSecretValue(string $secretName, string $version = 'latest'): string
    {
        $name = SecretManagerServiceClient::secretVersionName(
            $this->projectId,
            $secretName,
            $version,
        );

        $request = (new AccessSecretVersionRequest())->setName($name);
        $response = $this->getClient()->accessSecretVersion($request);

        return $response->getPayload()->getData();
    }

    public function updateSecret(string $secretName, string $newValue): void
    {
        $parent = SecretManagerServiceClient::secretName($this->projectId, $secretName);

        $payload = (new SecretPayload())->setData($newValue);
        $request = (new AddSecretVersionRequest())
            ->setParent($parent)
            ->setPayload($payload);

        $this->getClient()->addSecretVersion($request);
    }

    public function createSecret(string $secretName, string $initialValue): void
    {
        $parent = SecretManagerServiceClient::projectName($this->projectId);

        $replication = (new Replication())
            ->setAutomatic(new Automatic());

        $secret = (new Secret())
            ->setReplication($replication);

        $createRequest = (new CreateSecretRequest())
            ->setParent($parent)
            ->setSecretId($secretName)
            ->setSecret($secret);

        $this->getClient()->createSecret($createRequest);

        $this->updateSecret($secretName, $initialValue);
    }

    protected function extractSecretName(string $fullName): string
    {
        $parts = explode('/', $fullName);

        return end($parts);
    }
}
