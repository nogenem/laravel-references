<?php

namespace Database\Seeders\Traits;

use Illuminate\Support\Facades\DB;

trait TruncateTable
{
    protected function truncate($table)
    {
        // Better than using the model's `truncate` cause the Pivot tables are cleared too
        // and you don't need to disable/enable FOREIGN_KEY_CHECKS
        DB::table($table)->delete();
        DB::statement("ALTER TABLE `$table` AUTO_INCREMENT = 1");
    }
}
