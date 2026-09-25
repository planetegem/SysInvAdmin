<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use OpenApi\Attributes as OA;

#[OA\Info(
    title: "SysInvAdmin public API",
    version: "1.0.0",
    description: "This is the API documentation for the SysInvAdmin API."
)]
#[OA\Server(
    url: "/",
    description: "Current Environment"
)]

class APIController extends Controller
{}
