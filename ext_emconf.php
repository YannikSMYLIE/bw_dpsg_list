<?php
$EM_CONF[$_EXTKEY] = [
    'title' => '[DPSG] Mailman Connector',
    'description' => 'Verbindet die NaMi mit Mailman und organisiert automatisch Verteiler.',
    'category' => 'module',
    'author' => 'Yannik Börgener',
    'author_email' => 'kontakt@boergener.de',
    'state' => 'alpha',
    'internal' => '',
    'uploadfolder' => '0',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '8.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.22-8.7.99',
            'bw_dpsg_core' => '0.0.5',
            'php' => '7.1.0-7.9.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
