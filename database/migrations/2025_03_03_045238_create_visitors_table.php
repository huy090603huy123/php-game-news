<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address'); // Lưu địa chỉ IP của người truy cập
            $table->timestamp('visited_at'); // Lưu thời gian truy cập
            $table->integer('duration')->nullable(); // Lưu thời gian truy cập (tính bằng giây)
            $table->integer('pages_visited')->nullable(); // Lưu số lượng trang đã xem
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('visitors');
    }
};
