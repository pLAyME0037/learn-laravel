<?php
namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;

trait ClientHints
{
    /**
     * Get client hint values (Sec-CH-UA and Sec-CH-UA-Platform) with fallbacks.
     *
     * @param Request $request
     * @return array{sec_ch_ua: string|null, sec_ch_ua_platform: string|null}
     */
    private function getClientHints(Request $request): array
    {
        $userAgent = (string) $request->userAgent();

        // Try common header variants (some proxies or clients normalize casing)
        $secChUa         = $request->header('Sec-CH-UA') ?: $request->header('sec-ch-ua') ?: null;
        $secChUaPlatform = $request->header('Sec-CH-UA-Platform') ?: $request->header('sec-ch-ua-platform') ?: null;

        // If Sec-CH-UA is present, try to make it a bit more readable (strip surrounding quotes)
        if ($secChUa) {
            // Remove unnecessary whitespace and keep the raw header as fallback
            $clean = preg_replace('/\s+/', ' ', trim($secChUa));
            // Optionally remove version tokens like ;v="116" to keep brands
            $clean   = preg_replace('/;v=\"?[0-9\.]+\"?/', '', $clean);
            $clean   = str_replace('"', '', $clean);
            $secChUa = trim($clean, " ,");
        }

        // If platform hint isn't provided, attempt to detect from User-Agent
        if (! $secChUaPlatform) {
            $secChUaPlatform = $this->detectPlatformFromUserAgent($userAgent);
        } else {
            // Clean platform header (strip quotes and whitespace)
            $secChUaPlatform = trim(str_replace('"', '', $secChUaPlatform));
        }

        // If UA hint missing, generate a simple browser string from User-Agent
        if (! $secChUa) {
            $secChUa = $this->parseUserAgentSimple($userAgent);
        }

        return [
            'sec_ch_ua'          => $secChUa,
            'sec_ch_ua_platform' => $secChUaPlatform,
        ];
    }

    /**
     * Simple user-agent parser to produce a concise browser string.
     */
    private function parseUserAgentSimple(string $userAgent): string
    {
        $browserPatterns = [
            'Chrome'  => '/Chrome\/([0-9.]+)/',
            'Firefox' => '/Firefox\/([0-9.]+)/',
            'Safari'  => '/Version\/([0-9.]+).*Safari\//',
            'Edge'    => '/Edg\/([0-9.]+)/',
            'Opera'   => '/OPR\/([0-9.]+)/',
        ];

        foreach ($browserPatterns as $browser => $pattern) {
            if (preg_match($pattern, $userAgent, $matches)) {
                return $browser . ' ' . ($matches[1] ?? '');
            }
        }

        return 'Unknown Browser';
    }

    /**
     * Detect platform name from user agent.
     */
    private function detectPlatformFromUserAgent(string $userAgent): string
    {
        $platforms = [
            'Windows' => '/Windows NT ([0-9.]+)/',
            'Mac'     => '/Macintosh|Mac OS X/',
            'iOS'     => '/iPhone|iPad|iPod/',
            'Android' => '/Android ([0-9.]+)/',
            'Linux'   => '/Linux/',
        ];

        foreach ($platforms as $platform => $pattern) {
            if (preg_match($pattern, $userAgent)) {
                return $platform;
            }
        }

        return 'Unknown Platform';
    }
}
