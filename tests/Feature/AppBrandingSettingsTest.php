<?php

namespace Tests\Feature;

use App\Filament\Admin\Pages\AppBrandingSettings;
use App\Models\AppSetting;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AppBrandingSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_the_application_title_and_logo(): void
    {
        Storage::fake('public');

        $admin = $this->userWithRole('admin');
        $this->actingAs($admin);
        Filament::setCurrentPanel('admin');

        $logo = UploadedFile::fake()->image('brand.png');

        Livewire::test(AppBrandingSettings::class)
            ->assertSet('data.app_title', config('app.name', 'Absensi'))
            ->set('data.app_title', 'Presensi Kampus')
            ->set('data.logo_path', $logo)
            ->call('save')
            ->assertHasNoErrors();

        $settings = AppSetting::current();

        $this->assertSame('Presensi Kampus', $settings?->app_title);
        $this->assertNotNull($settings?->logo_path);
        Storage::disk('public')->assertExists($settings->logo_path);
        $this->assertSame('Presensi Kampus', Filament::getBrandName());
        $this->assertStringContainsString('Presensi Kampus', Filament::getBrandLogo()->toHtml());
    }

    public function test_non_admin_cannot_access_application_branding_settings(): void
    {
        $member = $this->userWithRole('team_member');

        $this->actingAs($member)
            ->get('/admin/pengaturan-aplikasi')
            ->assertForbidden();
    }

    public function test_settings_navigation_group_is_displayed_last(): void
    {
        $this->actingAs($this->userWithRole('admin'));
        Filament::setCurrentPanel('admin');

        $groupLabels = collect(Filament::getNavigation())
            ->map(fn ($group): ?string => $group->getLabel())
            ->filter()
            ->values();

        $this->assertSame('Pengaturan', $groupLabels->last());
    }

    private function userWithRole(string $roleName): User
    {
        $role = Role::findOrCreate($roleName, 'web');
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }
}
