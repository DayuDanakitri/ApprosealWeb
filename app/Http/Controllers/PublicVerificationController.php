<?php

namespace App\Http\Controllers;

use App\Models\Document;

class PublicVerificationController extends Controller
{
    /**
     * Display the public document verification page.
     */
    public function show(string $token)
    {
        $document = Document::with([
            'documentType',
            'versions',
        ])
            ->where('verification_token', $token)
            ->where('application_status', 'published')
            ->whereNotNull('published_at')
            ->firstOrFail();

        $currentVersion = $document->versions
            ->firstWhere('version_number', $document->current_version);

        return view('verification.show', compact(
            'document',
            'currentVersion'
        ));
    }
}
