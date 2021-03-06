<?php
return Symfony\CS\Config\Config::create()
    ->fixers(
        array(
            'encoding',
            'short_tag',
            'braces',
            'elseif',
            'function_call_space',
            'function_declaration',
            'indentation',
            'line_after_namespace',
            'linefeed',
            'lowercase_constants',
            'lowercase_keywords',
            'method_argument_space',
            'multiple_use',
            'unused_use',
            'remove_leading_slash_use',
            'parenthesis',
            'php_closing_tag',
            'trailing_spaces',
            'duplicate_semicolon',
            'extra_empty_lines',
            'join_function',
            'multiline_array_trailing_comma',
            'namespace_no_leading_whitespace',
            'no_blank_lines_after_class_opening',
            'no_empty_lines_after_phpdocs',
            'operators_spaces',
            'phpdoc_indent',
            'phpdoc_no_package',
            'phpdoc_params',
            'phpdoc_scalar',
            /*'single_line_after_imports', // Not working correctly at the moment */
            'phpdoc_separation',
            'whitespacy_lines',
            'align_double_arrow',
            'align_equals',
            'ereg_to_preg',
            'single_quote',
            'long_array_syntax',
            'phpdoc_trim',
            'return',
            'spaces_before_semicolon',
        )
    )
    ->level(Symfony\CS\FixerInterface::NONE_LEVEL)
    ->finder(
        Symfony\CS\Finder\DefaultFinder::create()->in(
            array(
                __DIR__ .'/apps/',
                __DIR__ .'/library/',
                __DIR__ .'/tests/Stubs/',
                __DIR__ .'/tests/Tests/',
            )
        )
    );