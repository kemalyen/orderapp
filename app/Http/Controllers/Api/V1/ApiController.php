<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller; 
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use App\Traits\ApiResponses;

class ApiController extends Controller
{
    public function include(string $relationship) : bool {
        $param = request()->get('include');

        if (!isset($param)) {
            return false;
        }

        $includeValues = explode(',', strtolower($param));

        return in_array(strtolower($relationship), $includeValues);
    }
 
}