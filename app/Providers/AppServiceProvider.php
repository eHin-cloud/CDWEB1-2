<?php

namespace App\Providers;

use App\Models\Room;
use App\Observers\RoomSolrObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::defaultView('partials.pagination');

        // Tự động đồng bộ thêm/sửa/xoá phòng sang Apache Solr
        Room::observe(RoomSolrObserver::class);
    }
}
