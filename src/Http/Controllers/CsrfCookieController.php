<?php

namespace AlirezaMoh\LaravelFileExplorer\Http\Controllers;

use Illuminate\Http\Response;

class CsrfCookieController
{
    public function getCsrf()
    {
        return response()->json([
            'data' => [
                'csrfToken' => csrf_token(),
            ]
        ], Response::HTTP_OK);
    }
}
