<?php

namespace App\Http\Controllers;

use App\Services\LoginSecurityService;
use Illuminate\Http\JsonResponse;

class SecurityCaptchaController extends Controller
{
    /**
     * Refresh and return a new SVG CAPTCHA challenge via AJAX.
     */
    public function refresh(LoginSecurityService $securityService): JsonResponse
    {
        $captchaData = $securityService->generateCaptchaSvg();

        return response()->json([
            'success' => true,
            'captcha_svg' => $captchaData['svg'],
        ]);
    }
}
