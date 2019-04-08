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
     * Search all type
     */
    public function searchMain($searchStruct, $searchAll, $from, $to)
    {
        $matches = [];
        if (!empty($searchStruct)) {
            foreach ($searchStruct as $field => $item) {
                $matches[] = [
                    'match' => [$field => $item]
                ];
            }
        }

        $paramsFullExample = [
            'index' => 'logs',
            'type' => 'test',
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            [
                                'multi_match' => [
                                    'query' => $searchAll,
                                    'operator' => 'and',
                                    'fields' => ['name', 'description'],
                                    'type' => 'cross_fields'],

                            ], $matches,
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
                    'operator' => 'and',
                    'fields' => ['name', 'description'],
                    'type' => 'cross_fields']
                ],
                $matches,
            ];
        }
        if (!empty($searchAll) && empty($matches)) {
            $params['body']['query']['bool']['must'] = [
                ['multi_match' => [
                    'query' => $searchAll,
                    'operator' => 'and',
                    'fields' => ['name', 'description'],
                    'type' => 'cross_fields']
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
                $paramsCost = [
                    'range' => [
                        'cost' => $arCost
                    ]
                ];
            }
            $params['body']['query']['bool']['must'][] =
                $paramsCost;
        }

//        ini_set("xdebug.var_display_max_children", -1);
//        ini_set("xdebug.var_display_max_data", -1);
//        ini_set("xdebug.var_display_max_depth", -1);
//        echo '<pre>', var_dump($params), '</pre>';
//        echo '<pre>', var_dump($paramsFullExample), '</pre>';
        return $this->client->search($params);
    }

    /**
     * Get all documents
     */
    public function getAllDocuments()
    {
        $params = [
            'index' => 'logs',
            'type' => 'test'
        ];
        try {
            return $this->client->search($params);
        } catch (\Exception $ex) {
            return false;
        }
    }
    /**
     * Get all documents
     */
    public function getDocumentById($idDocument)
    {

        $params = [
            'index' => 'logs',
            'type' => 'test',
            'id' => $idDocument
        ];
        try {
            $this->client->get($params);
        } catch (\Exception $exception) {
             return false;
        }
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
//        echo '<pre>', var_dump($params), '</pre>';
        return $this->client->index($params);
    }

    /**
     * Create mapping with nGram
     */
    public function createMapping()
    {
        $params = [
            'index' => 'logs',
            'body' => [
                'settings' => [
                    'analysis' => [
                        'analyzer' => [
                            'autocomplete' => [
                                'tokenizer' => 'autocomplete',
                                'filter' => [
                                    'lowercase'
                                ]
                            ],
                            'autocomplete_search' => [
                                'tokenizer' => 'lowercase'
                            ]
                        ],
                        'tokenizer' => [
                            'autocomplete' => [
                                'type' => 'edge_ngram',
                                'min_gram' => '2',
                                'max_gram' => '10',
                                'token_chars' => [
                                    'letter'
                                ]
                            ]
                        ]
                    ]
                ],
                'mappings' => [
                    'test' => [
                        'properties' => [
                            'id' => [
                                'type' => 'text'
                            ],
                            'transactionType' => [
                                'type' => 'text'
                            ],
                            'typeBulding' => [
                                'type' => 'text'
                            ],
                            'cost' => [
                                'type' => 'long'
                            ],
                            'square' => [
                                'type' => 'long'
                            ],
                            'rooms' => [
                                'type' => 'integer'
                            ],
                            'finish' => [
                                'type' => 'text'
                            ],
                            'trim' => [
                                'type' => 'text'
                            ],
                            'fund' => [
                                'type' => 'text'
                            ],
                            'accomodationFormat' => [
                                'type' => 'text'
                            ],
                            'mandatoryConditions' => [
                                'type' => 'text'
                            ],
                            'name' => [
                                'type' => 'text',
                                'analyzer' => 'autocomplete',
                                'search_analyzer' => 'autocomplete_search'
                            ],
                            'description' => [
                                'type' => 'text',
                                'analyzer' => 'autocomplete',
                                'search_analyzer' => 'autocomplete_search'
                            ]
                        ]
                    ]
                ]
            ]
        ];
        return $this->client->indices()->create($params);
    }

    /**
     * Delete by index
     */
    public function deleteByIndex(array $delIndex)
    {
        $deleteParams = [
            'index' => $delIndex,
//            'type' => 'data'
        ];
        return $this->client->indices()->delete($deleteParams);

    }

    /**
     * Delete by index
     */
    public function deleteById($idDocument)
    {
        $deleteParams = [
            'index' => 'logs',
            'type' => 'test',
            'id' => $idDocument
        ];
        return $this->client->delete($deleteParams);

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