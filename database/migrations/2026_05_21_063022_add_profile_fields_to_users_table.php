<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('guru')->change();
            $table->enum('gender', ['L', 'P'])->nullable()->after('phone');
            $table->string('nip')->nullable()->after('gender');
            $table->string('nuptk')->nullable()->after('nip');
            $table->string('username')->nullable()->unique()->after('nuptk');
            $table->text('address')->nullable()->after('username');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['gender', 'nip', 'nuptk', 'username', 'address']);
            $table->enum('role', ['guru', 'admin'])->default('guru')->change();
        });
    }
};
