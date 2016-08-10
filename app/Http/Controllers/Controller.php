<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Dingo\Api\Routing\Helpers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;

class Controller extends BaseController
{
    use Helpers, AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;
}
