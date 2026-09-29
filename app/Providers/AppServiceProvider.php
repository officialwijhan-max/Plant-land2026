<?php

namespace App\Providers;

use App\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Notification;
use Modules\ModuleManager\Entities\InfixModuleManager;
use Modules\RolePermission\Entities\Role;
use Modules\Setting\Model\BusinessSetting;
use Modules\Setting\Model\GeneralSetting;
use Nwidart\Modules\Facades\Module;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        config([
            'spondonit.module_manager_model' => InfixModuleManager::class,
            'spondonit.module_manager_table' => 'infix_module_managers',
            'spondonit.settings_model' => GeneralSetting::class,
            'spondonit.module_model' => Module::class,
            'spondonit.user_model' => User::class,
            'spondonit.settings_table' => 'general_settings',
        ]);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();
        view()->composer('sale::pos_order.components.header_menu', function ($view) {
            $data = [
                'notifications' => Notification::with('notifiable')->where('read_at',null)->where('user_id',null)
                    ->where('role',null)->latest()->get(),
            ];
            $view->with($data);
        });

        $this->bootAppState();
    }

    /**
     * Core app bootstrapping that used to live in the (now removed)
     * spondonit/biz-service licensing package: general/business settings,
     * locale, the admin menu notification composer, and query helpers used
     * across many module repositories.
     *
     * The business_settings/general_setting singletons and the
     * whereLike()/uniqueAccountName validator are read by repository code
     * regardless of whether it's invoked over HTTP or from the CLI (e.g.
     * db:seed calling SaleRepository::statusChange()), so only the
     * genuinely HTTP-only bits (session locale, the auth()-dependent menu
     * notification composer) are skipped in console.
     *
     * @return void
     */
    protected function bootAppState()
    {
        try {
            if (! Schema::hasTable('general_settings')) {
                return;
            }
        } catch (\Exception $e) {
            return;
        }

        app()->singleton('business_settings', function () {
            return BusinessSetting::select('type', 'status')->get();
        });

        app()->singleton('general_setting', function () {
            return GeneralSetting::first();
        });

        app()->singleton('permission_list', function () {
            return Role::with(['permissions' => function ($query) {
                $query->select('route', 'module_id', 'parent_id', 'role_id');
            }])->get(['id', 'name']);
        });

        if (! $this->app->runningInConsole()) {
            if (session()->has('locale')) {
                $locale = session()->get('locale');
            } else {
                $locale = app('general_setting')->language_name ?? 'en';
                session()->put('locale', $locale);
            }

            \App::setLocale($locale);

            config([
                'settings' => app('general_setting'),
                'bus_setting' => '',
            ]);

            view()->composer('backEnd.partials.menu', function ($view) {
                $data = [
                    'notifications' => Notification::with('notifiable')->where('user_id', auth()->user()->id)->where('role', auth()->user()->role_id)->where('read_at', null)->latest()->get(),
                ];
                $view->with($data);
            });
        }

        Validator::extend('uniqueAccountName', function ($attribute, $value, $parameters, $validator) {
            $count = DB::table('chart_accounts')->where('type', $value)
                ->where('name', $parameters[0])
                ->count();

            return $count === 0;
        });

        Builder::macro('whereLike', function ($attributes, string $searchTerm) {
            $this->where(function (Builder $query) use ($attributes, $searchTerm) {
                foreach (Arr::wrap($attributes) as $attribute) {
                    $query->when(
                        Str::contains($attribute, '.'),
                        function (Builder $query) use ($attribute, $searchTerm) {
                            [$relationName, $relationAttribute] = explode('.', $attribute);

                            $query->orWhereHas($relationName, function (Builder $query) use ($relationAttribute, $searchTerm) {
                                $query->where($relationAttribute, 'LIKE', "%{$searchTerm}%");
                            });
                        },
                        function (Builder $query) use ($attribute, $searchTerm) {
                            $query->orWhere($attribute, 'LIKE', "%{$searchTerm}%");
                        }
                    );
                }
            });
            return $this;
        });
    }
}
