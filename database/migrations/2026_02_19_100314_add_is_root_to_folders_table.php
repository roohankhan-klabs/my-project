<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('folders', function (Blueprint $table) {
            $table->boolean('is_root')->default(false)->after('parent_id');
            $table->index(['user_id', 'is_root']);
        });

        $now = now();

        $userIds = DB::table('users')->pluck('id');

        foreach ($userIds as $userId) {
            $rootId = DB::table('folders')
                ->where('user_id', $userId)
                ->where('is_root', true)
                ->value('id');

            if (! $rootId) {
                $rootId = DB::table('folders')->insertGetId([
                    'user_id' => $userId,
                    'parent_id' => null,
                    'is_root' => true,
                    'name' => 'Root',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            DB::table('folders')
                ->where('user_id', $userId)
                ->whereNull('parent_id')
                ->where('is_root', false)
                ->update([
                    'parent_id' => $rootId,
                    'updated_at' => $now,
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('folders', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'is_root']);
            $table->dropColumn('is_root');
        });
    }
};
