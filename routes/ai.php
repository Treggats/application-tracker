<?php

use App\Mcp\Servers\ApplicationTrackerServer;
use Laravel\Mcp\Facades\Mcp;

 Mcp::local('application-tracker', ApplicationTrackerServer::class);
