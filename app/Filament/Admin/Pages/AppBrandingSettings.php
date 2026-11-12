<?php

namespace App\Filament\Admin\Pages;

use App\Models\AppSetting;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;
use UnitEnum;

class AppBrandingSettings extends Page
{
    protected string $view = 'filament.admin.pages.app-branding-settings';

    protected static ?string $title = 'Pengaturan Aplikasi';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPaintBrush;

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Judul & Logo';

    protected static ?string $slug = 'pengaturan-aplikasi';

    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public function mount(): void
    {
        $settings = AppSetting::current();

        $this->form->fill([
            'app_title' => $settings?->app_title ?? config('app.name', 'Absensi'),
            'logo_path' => $settings?->logo_path,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('app_title')
                    ->label('Judul aplikasi')
                    ->required()
                    ->maxLength(120)
                    ->helperText('Judul akan ditampilkan bersama logo pada panel admin.'),
                FileUpload::make('logo_path')
                    ->label('Logo aplikasi')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(2048)
                    ->disk('public')
                    ->directory('branding')
                    ->visibility('public')
                    ->helperText('Format JPG, PNG, atau WebP. Ukuran maksimal 2 MB.'),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $settings = AppSetting::query()->find(1);
        $previousLogoPath = $settings?->logo_path;

        $settings = AppSetting::query()->updateOrCreate(
            ['id' => 1],
            [
                'app_title' => $state['app_title'],
                'logo_path' => $state['logo_path'] ?? null,
            ],
        );

        if (filled($previousLogoPath) && $previousLogoPath !== $settings->logo_path) {
            Storage::disk('public')->delete($previousLogoPath);
        }

        Notification::make()
            ->title('Pengaturan aplikasi berhasil disimpan')
            ->success()
            ->send();
    }
}
