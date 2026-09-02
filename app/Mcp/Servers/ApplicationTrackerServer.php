<?php

declare(strict_types=1);

namespace App\Mcp\Servers;

use App\Mcp\Tools\ListApplicationsTool;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('Application Tracker Server')]
#[Version('0.0.1')]
#[Instructions('Instructions describing how to use the server and its features.')]
final class ApplicationTrackerServer extends Server
{
    protected array $tools = [
        ListApplicationsTool::class,
    ];

    protected array $resources = [
        //
    ];

    protected array $prompts = [
        //
    ];
}
