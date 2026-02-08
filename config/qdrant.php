<?php

return [
    'host' => env('QDRANT_HOST', 'http://127.0.0.1'),
    'port' => env('QDRANT_PORT', 6333),
    'collection' => env('QDRANT_COLLECTION', 'chunks'),
    'url' => env('QDRANT_HOST', 'http://127.0.0.1') . ":" . env('QDRANT_PORT', 6333),
    'timeout' => 10,
];
