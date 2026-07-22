<?php

$fileHeaderComment = '';

$finder = PhpCsFixer\Finder::create()
    ->in('src')
    ->in('tests');

$config = (new PhpCsFixer\Config());

return $config
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'header_comment' => ['header' => $fileHeaderComment, 'separate' => 'both'],
    ])
    ->setFinder($finder);
