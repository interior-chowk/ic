<?php
namespace App\Services;

use App\Model\SeoMeta;

class SeoService
{
    public static function getSeoData(): ?SeoMeta
    {
        $route = request()->route();
        $name = $route?->getName(); // null if not named
        $params = implode(':', $route?->parameters() ?? []);
        $path = request()->path(); // fallback to URL path like 'seller/auth/seller-login'

        // Build search keys in priority: name with params > name > path
        $keys = [];

        if ($name && $params) {
            $keys[] = "$name:$params";
        }

        if ($name) {
            $keys[] = $name;
        }

        $keys[] = $path;

        return SeoMeta::whereIn('page', $keys)->first();
    }
}
