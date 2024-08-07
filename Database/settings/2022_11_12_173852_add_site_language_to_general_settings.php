<?php

declare(strict_types=1);

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class AddSiteLanguageToGeneralSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.site_language', config('app.fallback_locale'));
    }
}
