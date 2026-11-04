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
        $this->app->bind(\Psr\Http\Client\ClientInterface::class, \GuzzleHttp\Client::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::defaultView('partials.pagination');

        if (class_exists(\Matchish\ScoutElasticSearch\Engines\ElasticSearchEngine::class)) {
            $this->app->make(\Laravel\Scout\EngineManager::class)->extend('elasticsearch', function () {
                $elasticsearch = app(\Elastic\Elasticsearch\Client::class);
                return new \Matchish\ScoutElasticSearch\Engines\ElasticSearchEngine($elasticsearch);
            });
        }

        // Tự động đồng bộ thêm/sửa/xoá phòng sang Apache Solr
        if (class_exists(RoomSolrObserver::class)) {
            Room::observe(RoomSolrObserver::class);
        }
    }
}
