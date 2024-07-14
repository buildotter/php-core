<?php

return (new PhpCsFixer\Config())
    ->setRules(
        [
            'strict_param' => true,
            'array_syntax' => ['syntax' => 'short'],
            'trailing_comma_in_multiline' => [
                'elements' => [
                    'arrays',
                    'arguments',
                    'parameters',
                    'match',
                ],
            ],
            'declare_strict_types' => true,
            'native_function_invocation' => [
                'include' => ['@all'],
                'exclude' => ['service', 'tagged_iterator'],
            ],
            'no_extra_blank_lines' => [
                'tokens' => [
                    'break',
                    'continue',
                    'extra',
                    'return',
                    'throw',
                    'use',
                    'parenthesis_brace_block',
                    'square_brace_block',
                    'curly_brace_block',
                ],
            ],
            'braces_position' => [
                'allow_single_line_anonymous_functions' => true,
                'allow_single_line_empty_anonymous_classes' => true
            ],
            'indentation_type' => true,
            'blank_line_after_namespace' => true,
            'single_line_after_imports' => true,
            'echo_tag_syntax' => true,
            'no_unneeded_final_method' => false,
            'no_useless_else' => true,
            'no_useless_return' => true,
            'ordered_imports' => [
                'imports_order' => ['class', 'function', 'const'],
                'sort_algorithm' => 'alpha',
            ],
            'ordered_traits' => false,
            'phpdoc_add_missing_param_annotation' => true,
            'phpdoc_order' => true,
            'phpdoc_types_order' => [
                'null_adjustment' => 'always_last',
                'sort_algorithm' => 'none',
            ],
            'phpdoc_annotation_without_dot' => false,
            'phpdoc_to_comment' => ['ignored_tags' => ['todo', 'psalm-suppress']],
            'phpdoc_align' => ['align' => 'left'],
            'php_unit_method_casing' => ['case' => 'snake_case'],
            'php_unit_test_case_static_method_calls' => ['call_type' => 'self'],
            'single_line_throw' => false,
            'nullable_type_declaration_for_default_null_value' => [
                'use_nullable_type_declaration' => true,
            ],
            'no_trailing_comma_in_singleline' => true,
        ]
    )
    ->setRiskyAllowed(true)
    ->setUsingCache(true)
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(
                [
                    'src',
                    'tests',
                ]
            )
    );
