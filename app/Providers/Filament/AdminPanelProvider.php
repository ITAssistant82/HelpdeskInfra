<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->favicon(asset('images/Logo_Infra.png') . '?v=' . filemtime(public_path('images/Logo_Infra.png')))
            ->brandName('InfraDesk')
            ->brandLogo(fn () => view('components.brand-logo'))
            ->brandLogoHeight('3rem')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->navigationGroups([
                'Employees',
                'Asset Switch',
                'Asset Access Point',
                'Ticketing',
                'Settings',
            ])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                \App\Filament\Widgets\StatsOverview::class,
                \App\Filament\Widgets\TicketTrendChart::class,
                \App\Filament\Widgets\MyActiveTickets::class,
                \App\Filament\Widgets\UserTicketStats::class,
                \App\Filament\Widgets\UserTicketStatusChart::class,
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->renderHook('panels::auth.login.form.before', fn () => new HtmlString('
                <style>.fi-simple-header-heading { display: none !important; }</style>
            '))
            ->renderHook(
                'panels::auth.login.form.after',
                fn () => new HtmlString('
                    <div style="position:relative; margin:16px 0;">
                        <div style="position:absolute; inset:0; display:flex; align-items:center;">
                            <div style="width:100%; border-top:1px solid #e5e7eb;"></div>
                        </div>
                        <div style="position:relative; display:flex; justify-content:center; font-size:12px;">
                            <span style="background:white; padding:0 8px; color:#9ca3af;">atau</span>
                        </div>
                    </div>
                    <a href="' . route('microsoft.login') . '"
                       style="display:flex; align-items:center; justify-content:center; gap:10px; width:100%; padding:10px 24px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; font-weight:500; color:#374151; background:white; text-decoration:none; box-sizing:border-box;">
                        <svg width="18" height="18" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg" style="flex-shrink:0;">
                            <rect x="1" y="1" width="9" height="9" fill="#f25022"/>
                            <rect x="11" y="1" width="9" height="9" fill="#7fba00"/>
                            <rect x="1" y="11" width="9" height="9" fill="#00a4ef"/>
                            <rect x="11" y="11" width="9" height="9" fill="#ffb900"/>
                        </svg>
                        Masuk dengan Microsoft
                    </a>
                '),
            );
    }
}
