<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

// Pre-register Symfony's error handler before PHPUnit starts tracking exception handlers.
// Without this, the kernel boot (debug=true) registers it mid-test and tearDown doesn't
// restore it, causing PHPUnit 11's "did not remove its own exception handlers" check to fail.
\Symfony\Component\ErrorHandler\ErrorHandler::register();
