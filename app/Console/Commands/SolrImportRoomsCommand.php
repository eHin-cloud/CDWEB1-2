<?php

namespace App\Console\Commands;

use App\Models\Room;
use App\Services\SolrSearchService;
use Illuminate\Console\Command;

class SolrImportRoomsCommand extends Command
{
    /**
     * Tên và chữ ký của lệnh console.
     *
     * @var string
     */
    protected $signature = 'solr:import-rooms {--flush : Xoá sạch toàn bộ index hiện có trước khi import}';

    /**
     * Mô tả lệnh console.
     *
     * @var string
     */
    protected $description = 'Đồng bộ toàn bộ dữ liệu phòng trọ từ MySQL sang Apache Solr Search Index';

    /**
     * Thực thi lệnh console.
     */
    public function handle(SolrSearchService $solrService): int
    {
        $this->info('🔍 Đang kiểm tra kết nối tới Apache Solr...');

        if (!$solrService->ping()) {
            $this->error('❌ Không thể kết nối tới Solr tại ' . env('SOLR_HOST', '127.0.0.1') . ':' . env('SOLR_PORT', 8983));
            $this->warn('Hãy đảm bảo container Solr đang chạy: docker ps');
            return Command::FAILURE;
        }

        $this->info('✅ Kết nối Solr thành công!');

        if ($this->option('flush')) {
            $this->warn('🧹 Đang dọn dẹp dữ liệu cũ trong Solr core...');
            $solrService->flush();
            $this->info('✅ Đã xoá sạch index cũ.');
        }

        $totalRooms = Room::count();
        if ($totalRooms === 0) {
            $this->warn('⚠️ Không có phòng trọ nào trong database để import.');
            return Command::SUCCESS;
        }

        $this->info("🚀 Bắt đầu import {$totalRooms} phòng trọ vào Apache Solr...");

        $bar = $this->output->createProgressBar($totalRooms);
        $bar->start();

        $imported = 0;
        Room::with('building')->chunk(100, function ($rooms) use ($solrService, $bar, &$imported) {
            $count = $solrService->indexRooms($rooms);
            $imported += $count;
            $bar->advance($rooms->count());
        });

        $bar->finish();
        $this->newLine(2);

        $this->info("🎉 Hoàn tất! Đã đồng bộ {$imported}/{$totalRooms} phòng trọ vào Apache Solr.");
        return Command::SUCCESS;
    }
}

