<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->enum('stream_status', ['idle', 'active', 'finished'])->default('idle')->after('manual_directions');
            $table->string('mux_stream_id')->nullable()->after('stream_status');
            $table->string('mux_playback_id')->nullable()->after('mux_stream_id');
            $table->json('simulcast_targets')->nullable()->after('mux_playback_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['stream_status', 'mux_stream_id', 'mux_playback_id', 'simulcast_targets']);
        });
    }
};
