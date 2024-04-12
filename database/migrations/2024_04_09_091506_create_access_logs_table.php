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
        Schema::create('access_logs', function (Blueprint $table) {
            $table->uuid('id');
            $table->json('log');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');

            $table->primary(['id', 'created_at']);
        });

        // partition the table by month
        $partitions = implode(",\n", array_reduce(range(2024, 2025), function (array $carry, int $year): array {
            return [
                ...$carry,
                ...array_reduce(range(1, 12), function (array $carry, int $month) use ($year): array {
                    $monthP = str_pad($month, 2, '0', STR_PAD_LEFT);
                    $carry[] = "PARTITION p{$year}{$monthP} VALUES LESS THAN ('{$year}-{$monthP}-01 00:00:00')";
                    return $carry;
                }, [])
            ];
        }, []));

        DB::statement("
            ALTER TABLE `access_logs` PARTITION BY RANGE COLUMNS(`created_at`) (
               {$partitions}
            );
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_logs');
    }
};
