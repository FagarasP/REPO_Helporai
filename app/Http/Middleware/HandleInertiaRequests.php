<?php

namespace App\Http\Middleware;

use App\Support\Modules\ModuleRegistry;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function __construct(private readonly ModuleRegistry $moduleRegistry) {}

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user,
            ],
            'portalNavigation' => [
                'admin' => $this->navigationForPortal('admin', $user),
                'company' => $this->navigationForPortal('company', $user),
                'freelancer' => $this->navigationForPortal('freelancer', $user),
            ],
        ];
    }

    private function navigationForPortal(string $portal, $user): array
    {
        if (! $user || ! $user->canAccessPortal($portal)) {
            return [];
        }

        return $this->moduleRegistry
            ->forPortal($portal)
            ->flatMap(function (array $module) {
                $navigationPath = $module['base_path'].'/navigation.php';

                return file_exists($navigationPath) ? require $navigationPath : [];
            })
            ->filter(fn (array $item) => $user->hasPermission($item['permission']))
            ->sortBy('order')
            ->values()
            ->all();
    }
}
