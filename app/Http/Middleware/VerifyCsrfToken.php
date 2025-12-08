<?php

namespace App\Http\Middleware;
use Closure;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'v1/*',
        'v2/*',
        'v3/*',
        'cc/*'
    ];

    /*protected $startTime;

    public function handle($request, Closure $next) {
        $this->startTime = microtime(true);
        $response = $next($request);
        $responseContent = $response->getContent();

        $apiLog = [
            'CRR_DATE' => date('Y-m-d H:i:s'),
            'URI' => $request->getUri(),
            'METHOD' => $request->getMethod(),
            'REQUEST_BODY' => $request->all(),
            'RESPONSE' => substr($responseContent, 0,50)
        ];
        $endTime      = microtime(true);
        $responseTime = $endTime - $this->startTime;
        $apiLogFile = getcwd()."/storage/api_log.txt";
        $myfile = fopen($apiLogFile, "a");
        fwrite($myfile, json_encode($apiLog)."\n");
        fwrite($myfile, "startTime=>".$this->startTime."-endTime=>".$endTime."\n");
        fwrite($myfile, "responseTime=>".$responseTime."\n\n");
        fclose($myfile);
        return $next($request);
    }*/
}
