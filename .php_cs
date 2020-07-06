<?php
return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2' => true,
        'declare_strict_types' => true,
        'array_syntax' => ['syntax' => 'short'],
        'blank_line_after_opening_tag' => true,
        'single_blank_line_before_namespace' => true,
        'no_unused_imports' => true,
        'single_quote' => true,
        'native_function_casing' => false,
        'native_function_invocation' => false,
        'single_import_per_statement' => true,
        'single_line_after_imports' => true,
        'blank_line_after_namespace' => true,
        'no_extra_blank_lines' => true,

        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ],

        'phpdoc_add_missing_param_annotation' => false,
        'phpdoc_align' => true,
        'phpdoc_annotation_without_dot' => true,
        'phpdoc_indent' => true,
        'phpdoc_no_access' => true,
        'phpdoc_no_empty_return' => true,
        'phpdoc_no_package' => true,
        'phpdoc_order' => true,
        'phpdoc_return_self_reference' => true,
        'phpdoc_scalar' => true,
        'phpdoc_separation' => true,
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_summary' => true,
        'phpdoc_to_comment' => true,
        'phpdoc_trim' => true,
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'phpdoc_types' => ['groups' => ['simple', 'meta']],
        'phpdoc_types_order' => true,
        'phpdoc_var_without_name' => true,
        'phpdoc_line_span' => true,

        'fully_qualified_strict_types' => true,
        'no_spaces_after_function_name' => true,
        'method_separation' => true,

        'class_attributes_separation' => ['elements' => ['const', 'method', 'property']]
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__ . '/src')
            ->in(__DIR__ . '/tests')
    )->setRiskyAllowed(true)
    ->setUsingCache(false);