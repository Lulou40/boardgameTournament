<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class BggClient
{
    private const BASE = 'https://boardgamegeek.com/xmlapi2';

    public function __construct(private HttpClientInterface $http)
    {
    }

    /**
     * @return array<int, array{id:int,name:string,year?:int}>
     */
    public function searchBoardgames(string $query, int $limit = 5): array
    {
        $resp = $this->http->request('GET', self::BASE . '/search', [
            'query' => [
                'query' => $query,
                'type' => 'boardgame',
            ],
            'headers' => [
                'User-Agent' => 'BGTournament/1.0 (+https://example.local; contact: you@example.local)',
                'Accept' => 'application/xml,text/xml;q=0.9,*/*;q=0.8',
                'Referer' => 'https://boardgamegeek.com/',
            ],
            
        ]);

        $xml = new \SimpleXMLElement($resp->getContent());
        $out = [];

        foreach ($xml->item as $item) {
            if (count($out) >= $limit) {
                break;
            }

            $id = (int) $item['id'];

            // name primary
            $name = '';
            foreach ($item->name as $n) {
                if ((string) $n['type'] === 'primary') {
                    $name = (string) $n['value'];
                    break;
                }
            }
            if ($name === '' && isset($item->name[0]['value'])) {
                $name = (string) $item->name[0]['value'];
            }

            $year = isset($item->yearpublished['value']) ? (int) $item->yearpublished['value'] : null;

            if ($id > 0 && $name !== '') {
                $row = ['id' => $id, 'name' => $name];
                if ($year) {
                    $row['year'] = $year;
                }
                $out[] = $row;
            }
        }

        return $out;
    }


    /**
     * @return array<string,mixed> {bgg_id,name,year,players_min,players_max,duration_minutes,publisher}
     */
    public function getThing(int $bggId): array
    {
        $resp = $this->http->request('GET', self::BASE . '/thing', [
            'query' => ['id' => $bggId],
            'headers' => [
                'User-Agent' => 'BGTournament/1.0 (+https://example.local; contact: you@example.local)',
                'Accept' => 'application/xml,text/xml;q=0.9,*/*;q=0.8',
                'Referer' => 'https://boardgamegeek.com/',
            ],
        ]);


        // BGG peut renvoyer un contenu temporaire vide/partiel → on protège un minimum
        $content = $resp->getContent();
        $xml = new \SimpleXMLElement($content);
        $item = $xml->item[0] ?? null;
        if (!$item)
            return [];

        $name = '';
        foreach ($item->name as $n) {
            if ((string) $n['type'] === 'primary') {
                $name = (string) $n['value'];
                break;
            }
        }

        $publisher = null;
        foreach ($item->link as $link) {
            if ((string) $link['type'] === 'boardgamepublisher') {
                $publisher = (string) $link['value'];
                break;
            }
        }

        $image = isset($item->image) ? (string) $item->image : null;
        $thumbnail = isset($item->thumbnail) ? (string) $item->thumbnail : null;


        return [
            'bgg_id' => $bggId,
            'name' => $name,
            'year' => isset($item->yearpublished['value']) ? (int) $item->yearpublished['value'] : null,
            'players_min' => isset($item->minplayers['value']) ? (int) $item->minplayers['value'] : null,
            'players_max' => isset($item->maxplayers['value']) ? (int) $item->maxplayers['value'] : null,
            'duration_minutes' => isset($item->playingtime['value']) ? (int) $item->playingtime['value'] : null,
            'publisher' => $publisher,
            'image' => $image,
            'thumbnail' => $thumbnail
        ];
    }

    /**
     * @param array<int,string|int> $bggIds
     * @return array<string,array{image:?string,thumbnail:?string}>
     */
    public function getThings(array $bggIds): array
    {
        // Nettoyage + limite safe
        $bggIds = array_values(array_filter(array_map(
            static fn($v) => preg_replace('/\D+/', '', (string) $v),
            $bggIds
        )));

        if (!$bggIds) {
            return [];
        }

        // Batch “safe”: 10–20 max (tu peux ajuster)
        $bggIds = array_slice($bggIds, 0, 20);

        $ids = implode(',', $bggIds);

        // Retry simple (BGG peut répondre 401/429/503 temporairement)
        $attempts = 3;
        $delaySeconds = 3;

        while ($attempts-- > 0) {
            $resp = $this->http->request('GET', self::BASE . '/thing', [
                'query' => ['id' => $ids],
                'headers' => [
                    'User-Agent' => 'BGTournament/1.0 (+https://example.local; contact: you@example.local)',
                    'Accept' => 'application/xml,text/xml;q=0.9,*/*;q=0.8',
                    'Referer' => 'https://boardgamegeek.com/',
                ],
            ]);

            $status = $resp->getStatusCode();

            // OK
            if ($status >= 200 && $status < 300) {
                $content = $resp->getContent(false);

                // Parfois réponse vide/partielle → on retry
                if (trim($content) === '') {
                    if ($attempts > 0) {
                        sleep($delaySeconds);
                        $delaySeconds *= 2;
                        continue;
                    }
                    return [];
                }

                try {
                    $xml = new \SimpleXMLElement($content);
                } catch (\Throwable) {
                    if ($attempts > 0) {
                        sleep($delaySeconds);
                        $delaySeconds *= 2;
                        continue;
                    }
                    return [];
                }

                $result = [];
                foreach ($xml->item as $item) {
                    $id = (string) $item['id'];
                    $result[$id] = [
                        'image' => isset($item->image) ? (string) $item->image : null,
                        'thumbnail' => isset($item->thumbnail) ? (string) $item->thumbnail : null,
                    ];
                }

                return $result;
            }

            // Retry pour codes “transitoires”
            if (in_array($status, [401, 429, 500, 502, 503], true) && $attempts > 0) {
                sleep($delaySeconds);
                $delaySeconds *= 2;
                continue;
            }

            // Sinon: on laisse HttpClient lever une exception avec le bon message
            $resp->getContent(); // déclenche exception
        }

        return [];
    }

}
