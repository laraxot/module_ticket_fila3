<?php

declare(strict_types=1);

namespace Modules\Ticket\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Illuminate\Contracts\Support\Htmlable;
use Modules\Ticket\Settings\GeneralSettings;
use Modules\User\Models\Role;
use Webmozart\Assert\Assert;

class ManageGeneralSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $settings = GeneralSettings::class;

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if (null === $user) {
            return false;
        }

        return $user->can('Manage general settings');
    }

    public function getHeading(): string|Htmlable
    {
        return __('Manage general settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('General');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Settings');
    }

    protected function getFormSchema(): array
    {
        Assert::integer($max_file_size = config('system.max_file_size'));

        return [
            Section::make()
                ->schema([
                    Grid::make(3)
                        ->schema([
                            FileUpload::make('site_logo')
                                ->label(__('Site logo'))
                                ->helperText(__('This is the platform logo (e.g. Used in site favicon)'))
                                ->image()
                                ->columnSpan(1)
                                ->maxSize($max_file_size),

                            Grid::make(1)
                                ->columnSpan(2)
                                ->schema([
                                    TextInput::make('site_name')
                                        ->label(__('Site name'))
                                        ->helperText(__('This is the platform name'))
                                        ->default(static fn () => config('app.name'))
                                        ->required(),

                                    Toggle::make('enable_registration')
                                        ->label(__('Enable registration?'))
                                        ->helperText(__('If enabled, any user can create an account in this platform. But an administration need to give them permissions.')),

                                    Toggle::make('enable_social_login')
                                        ->label(__('Enable social login?'))
                                        ->helperText(__('If enabled, configured users can login via their social accounts.')),

                                    Toggle::make('enable_login_form')
                                        ->label(__('Enable form login?'))
                                        ->helperText(__('If enabled, a login form will be visible on the login page.')),

                                    Toggle::make('enable_oidc_login')
                                        ->label(__('Enable OIDC login?'))
                                        ->helperText(__('If enabled, an OIDC Connect button will be visible on the login page.')),

                                    Select::make('site_language')
                                        ->label(__('Site language'))
                                        ->helperText(__('The language used by the platform.'))
                                        ->searchable()
                                        ->options($this->getLanguages()),
                                    /*
                                    Select::make('default_role')
                                        ->label(__('Default role'))
                                        ->helperText(__('The platform default role (used mainly in OIDC Connect).'))
                                        ->searchable()
                                        ->options(Role::all()->pluck('name', 'id')->toArray()),
                                    */
                                ]),
                        ]),
                ]),
        ];
    }

    public function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()->label(__('Save'));
    }

    private function getLanguages(): array
    {
        Assert::isArray($languages = config('system.locales.list'));
        asort($languages);

        return $languages;
    }
}
