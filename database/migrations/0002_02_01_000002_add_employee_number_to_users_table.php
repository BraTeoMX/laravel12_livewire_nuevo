<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // La columna puede ya existir desde un intento anterior.
        // Si no existe, la creamos. Si existe, la modificamos.

        if (!Schema::hasColumn('users', 'employee_number')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('employee_number')->nullable()->after('id');
            });
        }

        // Asignar valores únicos a registros que tengan null o 0
        DB::table('users')
            ->whereNull('employee_number')
            ->orWhere('employee_number', 0)
            ->orderBy('id')
            ->chunk(100, function ($users) {
                foreach ($users as $user) {
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['employee_number' => $user->id + 1000]);
                }
            });

        // Hacer la columna única y no nullable
        Schema::table('users', function (Blueprint $table) {
            $table->integer('employee_number')->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('employee_number');
        });
    }
};
