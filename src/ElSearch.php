<?php

namespace Elastic;

use Elasticsearch\ClientBuilder;

class ElSearch
{
    protected $client;

    public function __construct()
    {
        $hosts = [
            '127.0.0.1:9200'         // IP + Port
        ];
        $this->client = ClientBuilder::create()
            ->setHosts($hosts)
            ->build();
    }

    /**
     * Search by fields
     */
    public function searchMain($searchStruct, $searchAll, $from, $to)
    {
        $matches = [];
        foreach ($searchStruct as $field => $item) {
            $matches[] = [
                'match' => [$field => $item]
            ];
        }

        $paramsExample = [
            'index' => 'logs',
            'type' => 'test',
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            ['multi_match' => [
                                'query' => $searchAll,
                                'fields' => ['name', 'description'],
                                'type' => 'phrase']
                            ],
                            $matches
                        ],
                        'filter' => [
                            [
                                'range' => [
                                    'cost' => [
                                        'gte' => $from,
                                        'lte' => $to
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $params = [
            'index' => 'logs',
            'type' => 'test',
            'body' => [
                'query' => [
                    'bool' => [
                    ]
                ]
            ]
        ];

        if (!empty($searchAll) && !empty($matches)) {
            $params['body']['query']['bool']['must'] = [
                ['multi_match' => [
                    'query' => $searchAll,
                    'fields' => ['name', 'description'],
                    'type' => 'phrase']
                ],
                $matches
            ];
        }
        if (!empty($searchAll) && empty($matches)) {
            $params['body']['query']['bool']['must'] = [
                ['multi_match' => [
                    'query' => $searchAll,
                    'fields' => ['name', 'description'],
                    'type' => 'phrase']
                ]
            ];
        } elseif (empty($searchAll) && !empty($matches)) {
            $params['body']['query']['bool']['must'] = [
                $matches
            ];
        }
        if (!empty($from) || !empty($to)) {
            $arCost = [];
            if (!empty($from))
                $arCost['gte'] = $from;
            if (!empty($to))
                $arCost['lte'] = $to;
            if (!empty($arCost)) {
                $params['body']['query']['bool']['filter'] = [
                    [
                        'range' => [
                            'cost' => $arCost
                        ]]
                ];
            }
        }
        return $this->client->search($params);
    }

    /**
     * Get all documents
     */
    public function getAllDocuments()
    {
        $params = [
            'index' => 'logs',
            'type' => 'test',
        ];
        return $this->client->search($params);
    }


    /**
     * Search by fields
     */
    public function search(array $searchStruct)
    {
        $matches = [];
        foreach ($searchStruct as $field => $item) {
            $matches[] = [
                'match' => [$field => $item]
            ];
        }
        $params = [
            'index' => 'logs',
            'type' => 'test',
            'body' => [
                'query' => [
                    "bool" => [
                        "must" => $matches
                    ]
                ]
            ]
        ];
        return $this->client->search($params);
    }

    /**
     * Search in all documents
     */
    public function searchAllbyFields($searchAll)
    {
        $params = [
            'index' => 'logs',
            'type' => 'test',
            'body' => [
                'query' => [
                    'multi_match' => [
                        'query' => $searchAll,
                        'fields' => ['name', 'description'],
                        'type' => 'most_fields'
                    ]
                ]
            ]
        ];
        return $this->client->search($params);
    }

    /**
     * Search by cost
     */
    public function searchByCost($from, $to)
    {
        $params = [
            'index' => 'logs',
            'type' => 'test',
            'body' => [
                'query' => [
                    'bool' => [
                        'filter' => [
                            'range' => [
                                'cost' => [
                                    'gte' => $from,
                                    'lte' => $to
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        return $this->client->search($params);
    }

    /**
     * Create index
     */
    public function create(array $addStruct)
    {
        $matches = [];
        foreach ($addStruct as $field => $item) {
            $matches[$field] = $item;
        }
        $params = [
            'index' => 'logs',
            'type' => 'test',
            'body' => $matches
        ];
        return $this->client->index($params);
    }

    /**
     * Delete by index
     */
    public function deleteByIndex(array $delIndex)
    {
        $deleteParams = [
            'index' => $delIndex
        ];
        try {
            $this->client->indices()->delete($deleteParams);
        } catch (\Throwable $e) {
            print_r('Index is incorrect');
        }
    }

    /**
     * Update document
     */
    public function updateById($idDocument, $updateField, $updateValue)
    {
        $params = [
            'index' => 'logs',
            'type' => 'test',
            'id' => $idDocument,
            'body' => [
                'doc' => [
                    $updateField => $updateValue
                ]
            ]
        ];
        return $this->client->update($params);
    }
}