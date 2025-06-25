<?php

namespace App\Service;

use MongoDB\Client;

class MongoService
{
    private $collection;

    public function __construct(string $mongoUrl)
    {
        $client = new Client($mongoUrl, [], ['ssl' => true]);

        $this->collection = $client->ecoride->chauffeur_preferences;
    }

    public function savePreferences(int $userId, array $preferences): void
    {
        $this->collection->updateOne(
            ['user_id' => $userId],
            ['$set' => ['preferences' => $preferences]],
            ['upsert' => true]
        );
    }

    public function getPreferences(int $userId): ?array
    {
        $doc = $this->collection->findOne(['user_id' => $userId]);
        return $doc['preferences'] ?? null;
    }
}
