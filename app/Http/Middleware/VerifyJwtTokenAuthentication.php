<?php

namespace App\Http\Middleware;

use App\Traits\JwtTokenizable;
use Closure;
use Illuminate\Http\Request;

class VerifyJwtTokenAuthentication
{
    use JwtTokenizable;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $header = $request->header('Authorization');
        try {
            if (!empty($header)) {
                $parts = explode(' ', $header);
                if (count($parts) < 2 || empty($parts[0]) || !preg_match('/^Bearer$/i', $parts[0])) {
                    throw new \Exception('Format is Authorization: Bearer [token].', 401);
                }
                $token = $parts[1];
            } else {
                throw new \Exception('Token is missing. Please pass the token in request in the form of header.', 401);
            }

            $payload = $this->checkToken($token);
            if (empty($payload) || !isSuccessResponse($payload)) {
                throw new \Exception('Invalid or empty token.', 401);
            }

            $payload = $payload['data'];
        } catch (\Exception $e) {
            $code = $e->getCode();
            $response = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
            return response()->json($response, $code);
        }

        $authorizationAttr = [
            'token' => $token,
            'payload' => $this->getLoginTokenData($payload),
        ];

        $request->merge(['authorization' => $authorizationAttr]);

        return $next($request);
    }
}
