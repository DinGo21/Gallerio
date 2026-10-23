<?php

declare(strict_types=1);

/*
 * Bootstrap file: loads configuration, helpers and the data layer.
 * Every entry point requires this file before doing any work.
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/data/images.php';
require_once __DIR__ . '/ImageRepository.php';
