<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function robots(): Response
    {
        $sitemap = url('/sitemap.xml');

        $body = implode("\n", [
            'User-agent: *',
            'Allow: /appointment/',
            'Allow: /book/',
            'Allow: /sitemap.xml',
            'Disallow: /login',
            'Disallow: /register',
            'Disallow: /verify',
            'Disallow: /prescriptions',
            'Disallow: /appointments',
            'Disallow: /appointment-config',
            'Disallow: /availability',
            'Disallow: /profile',
            'Disallow: /rx/',
            'Disallow: /livewire',
            '',
            'Sitemap: '.$sitemap,
            '',
        ]);

        return response($body, 200)->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    public function sitemap(): Response
    {
        $urls = [];

        $clinics = User::query()
            ->whereNotNull('appointment_slug')
            ->where('appointment_slug', '!=', '')
            ->where(function ($query) {
                $query->whereNotNull('email_verified_at')
                    ->orWhereNotNull('phone_verified_at');
            })
            ->orderBy('id')
            ->get(['appointment_slug', 'updated_at', 'clinic_name']);

        foreach ($clinics as $clinic) {
            $urls[] = [
                'loc' => route('appointment.public', $clinic->appointment_slug),
                'changefreq' => 'weekly',
                'priority' => '0.9',
                'lastmod' => optional($clinic->updated_at)->toAtomString() ?: now()->toAtomString(),
            ];
            $urls[] = [
                'loc' => route('appointment.book', $clinic->appointment_slug),
                'changefreq' => 'weekly',
                'priority' => '0.8',
                'lastmod' => optional($clinic->updated_at)->toAtomString() ?: now()->toAtomString(),
            ];
        }

        $xml = view('seo.sitemap', ['urls' => $urls])->render();

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
