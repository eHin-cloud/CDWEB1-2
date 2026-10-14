<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Bổ sung Search Indexes chuyên sâu cho việc tìm kiếm theo mã phòng, số điện thoại, tên tòa nhà và địa chỉ
     */
    public function up(): void
    {
        $isMysql = DB::getDriverName() === 'mysql';

        // 1. Chỉ mục tìm kiếm mã phòng và mô tả phòng (Bảng rooms)
        Schema::table('rooms', function (Blueprint $table) use ($isMysql) {
            // Index độc lập cho mã / số phòng (room_number) để tìm kiếm trực tiếp theo số phòng
            $table->index('room_number', 'rooms_room_number_idx');

            // Fulltext Index cho phần mô tả phòng trọ (chỉ trên MySQL)
            if ($isMysql) {
                $table->fullText('description', 'rooms_description_fulltext');
            }
        });

        // 2. Chỉ mục tìm kiếm địa chỉ, quận huyện, tên đường và tên cơ sở (Bảng buildings)
        Schema::table('buildings', function (Blueprint $table) use ($isMysql) {
            // Tìm kiếm trực tiếp theo tên cơ sở / khu trọ
            $table->index('name', 'buildings_name_idx');

            // Tìm kiếm trực tiếp theo địa chỉ / tên đường / quận huyện
            $table->index('address', 'buildings_address_idx');

            // Fulltext Index để tìm kiếm toàn văn khu vực và tên đường (chỉ trên MySQL)
            if ($isMysql) {
                $table->fullText(['name', 'address'], 'buildings_name_address_fulltext');
            }
        });

        // 3. Chỉ mục tìm kiếm theo số điện thoại khách liên hệ hẹn xem phòng (Bảng contact_requests)
        Schema::table('contact_requests', function (Blueprint $table) {
            $table->index('phone', 'contact_requests_phone_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $isMysql = DB::getDriverName() === 'mysql';

        Schema::table('rooms', function (Blueprint $table) use ($isMysql) {
            $table->dropIndex('rooms_room_number_idx');
            if ($isMysql) {
                $table->dropIndex('rooms_description_fulltext');
            }
        });

        Schema::table('buildings', function (Blueprint $table) use ($isMysql) {
            $table->dropIndex('buildings_name_idx');
            $table->dropIndex('buildings_address_idx');
            if ($isMysql) {
                $table->dropIndex('buildings_name_address_fulltext');
            }
        });

        Schema::table('contact_requests', function (Blueprint $table) {
            $table->dropIndex('contact_requests_phone_idx');
        });
    }
};
