<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class AppSetting extends Model
{
    protected $fillable = [
        'app_title',
        'logo_path',
    ];

    public static function current(): ?self
    {
        return static::query()->find(1);
    }

    public static function currentTitle(): string
    {
        return static::current()?->app_title ?: config('app.name', 'Absensi');
    }

    public static function currentBrandLogo(): ?HtmlString
    {
        $settings = static::current();
        $logoPath = $settings?->logo_path;

        if (blank($logoPath)) {
            return null;
        }

        $logoUrl = e(Storage::disk('public')->url($logoPath));
        $title = e($settings->app_title ?: config('app.name', 'Absensi'));

        return new HtmlString(
            '<span style="display:inline-flex;align-items:center;gap:.625rem;height:100%">' .
                '<img src="' . $logoUrl . '" alt="" style="height:2rem;width:auto;max-width:5rem;object-fit:contain">' .
                '<span>' . $title . '</span>' .
                '</span>',
        );
    }
}
