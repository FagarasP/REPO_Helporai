<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ModulePageController extends Controller
{
    public function __invoke(Request $request, string $portal, string $module): Response
    {
        return Inertia::render('Modules/PlaceholderPage', [
            'portal' => $portal,
            'module' => $module,
            'titleKey' => "{$portal}.modules.{$module}.title",
            'descriptionKey' => "{$portal}.modules.{$module}.description",
        ]);
    }
}
