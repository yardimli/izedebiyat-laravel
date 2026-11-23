<?php

namespace App\Services;

use GeoIp2\Database\Reader;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\IpUtils;

class VisitorCountry
{
    public function ip(Request $request): string
    {
        $peer = (string) $request->server('REMOTE_ADDR', '');
        // Only an explicitly trusted edge may supply the visitor address.
        if (config('human-check.trusted_proxies') && IpUtils::checkIp($peer, config('human-check.trusted_proxies'))) {
            $forwarded = $request->header('CF-Connecting-IP');
            if (! $forwarded) {
                // Walk from the nearest hop; never trust an attacker-prepended first IP.
                foreach (array_reverse(explode(',', $request->header('X-Forwarded-For', ''))) as $hop) {
                    $hop = trim($hop);
                    if (! filter_var($hop, FILTER_VALIDATE_IP)) {
                        return $peer;
                    }
                    if (! IpUtils::checkIp($hop, config('human-check.trusted_proxies'))) {
                        $forwarded = $hop;
                        break;
                    }
                }
            }
            if (filter_var($forwarded, FILTER_VALIDATE_IP)) {
                return $forwarded;
            }
        }

        return $peer;
    }

    public function country(string $ip): ?string
    {
        $reader = null;
        try {
            $reader = new Reader(config('human-check.database'));

            return $reader->country($ip)->country->isoCode;
        } catch (\Throwable $e) {
            // Unknown addresses / missing database must not silently bypass the gate.
            return null;
        } finally {
            $reader?->close();
        }
    }
}
