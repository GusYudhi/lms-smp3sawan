<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DropSchoolTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'table:drop-school';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop and recreate school_profiles table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->confirm('Are you sure you want to drop school_profiles table?')) {
            try {
                Schema::dropIfExists('school_profiles');
                $this->info('Table school_profiles dropped successfully!');

                // Remove migration entry
                DB::table('migrations')
                    ->where('migration', '2025_11_13_081231_create_school_profiles_table')
                    ->delete();

                $this->info('Migration entry removed.');
                $this->line('Now run: php artisan migrate to recreate the table with proper structure.');

            } catch (\Exception $e) {
                $this->error('Error: ' . $e->getMessage());
                return 1;
            }
        }

        return 0;
    }
}
