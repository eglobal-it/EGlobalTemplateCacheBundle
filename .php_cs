<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in(__DIR__)
;

return Symfony\CS\Config\Config::create()
    ->fixers([
        '-concat_without_spaces',
        'short_array_syntax',
        'ordered_use',
        'concat_with_spaces',
        '-multiple_use'
    ])
    ->finder($finder)
;
