<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE form_entries MODIFY COLUMN form_type ENUM(
                'trade_in',
                'get_approved',
                'unlock_calculator',
                'managers_special',
                'ask_question',
                'schedule_test_drive',
                'contact_us',
                'unlock_eprice',
                'schedule_service',
                'detailing'
            ) NOT NULL");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE form_entries MODIFY COLUMN form_type ENUM(
                'trade_in',
                'get_approved',
                'unlock_calculator',
                'managers_special',
                'ask_question',
                'schedule_test_drive',
                'contact_us',
                'unlock_eprice',
                'schedule_service'
            ) NOT NULL");
        }
    }
};
