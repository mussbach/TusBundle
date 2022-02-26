<?php

$finder = PhpCsFixer\Finder::create()
    ->in('src')
    ->in('tests');

$config = new PhpCsFixer\Config();
return $config
    ->setRiskyAllowed(true)
    ->setRules(
        [
            'array_syntax'           => ['syntax' => 'short'],
            '@PSR2'                  => true,
            '@Symfony'               => true,
            'binary_operator_spaces' => ['default' => 'align', 'operators' => ['=' => 'single_space']],
        ]
    )
    ->setFinder($finder);
