<?php

declare(strict_types=1);

return [
    'preset' => 'default',
    'exclude' => [
        //  'path/to/directory-or-file'
    ],
    'add' => [
        //  ExampleMetric::class => [
        //      ExampleInsight::class,
        //  ]
    ],
    'remove' => [
        \SlevomatCodingStandard\Sniffs\Classes\ForbiddenPublicPropertySniff::class,
        \NunoMaduro\PhpInsights\Domain\Sniffs\ForbiddenSetterSniff::class,
        \PhpCsFixer\Fixer\ClassNotation\ProtectedToPrivateFixer::class,
        \SlevomatCodingStandard\Sniffs\TypeHints\DisallowMixedTypeHintSniff::class,
        \SlevomatCodingStandard\Sniffs\Functions\StaticClosureSniff::class,
        \SlevomatCodingStandard\Sniffs\Classes\SuperfluousInterfaceNamingSniff::class,
        \SlevomatCodingStandard\Sniffs\Classes\SuperfluousExceptionNamingSniff::class,
        \PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff::class,
    ],
    'config' => [
        //  ExampleInsight::class => [
        //      'key' => 'value',
        //  ],
    ],
];
