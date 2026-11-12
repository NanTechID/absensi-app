<?php

namespace App\Http\Responses;

use App\Filament\Admin\Resources\Absensis\AbsensiResource;
use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class FilamentLoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        $user = $request->user();

        $url = $user?->hasRole('admin')
            ? route('filament.admin.pages.dashboard')
            : AbsensiResource::getUrl('index', panel: 'admin');

        return redirect()->to($url);
    }
}
