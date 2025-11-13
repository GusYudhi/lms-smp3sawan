<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DescribeTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'table:describe {table}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Describe table structure';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $table = $this->argument('table');

        try {
            $columns = DB::select("DESCRIBE $table");

            $this->info("Structure of table: $table");
            $this->line('');

            foreach ($columns as $column) {
                $this->line("Field: {$column->Field}, Type: {$column->Type}, Null: {$column->Null}, Key: {$column->Key}, Default: {$column->Default}, Extra: {$column->Extra}");
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
