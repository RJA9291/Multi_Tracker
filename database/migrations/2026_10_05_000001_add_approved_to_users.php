<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'approved')) {
                $table->boolean('approved')->default(false);
            }
        });

        // Grandfather every existing account so current users aren't locked out.
        DB::table('users')->update(['approved' => true]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'approved')) {
                $table->dropColumn('approved');
            }
        });
    }
};
