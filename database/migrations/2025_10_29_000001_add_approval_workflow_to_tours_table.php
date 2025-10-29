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
        Schema::table('tours', function (Blueprint $table) {
            // Modify status column to include new approval statuses
            $table->enum('status', [
                'draft',
                'pending_approval',
                'approved',
                'rejected',
                'active',
                'inactive',
                'suspended',
                'archived'
            ])->default('draft')->change();

            // Add approval workflow fields
            $table->foreignId('created_by_operator_user_id')
                ->nullable()
                ->after('tour_operator_id')
                ->constrained('tour_operator_users')
                ->nullOnDelete()
                ->comment('Operator user who created this tour');

            $table->foreignId('approved_by_admin_id')
                ->nullable()
                ->after('created_by_operator_user_id')
                ->constrained('admin_users')
                ->nullOnDelete()
                ->comment('Admin user who approved this tour');

            $table->timestamp('submitted_at')
                ->nullable()
                ->after('approved_by_admin_id')
                ->comment('When tour was submitted for approval');

            $table->timestamp('approved_at')
                ->nullable()
                ->after('submitted_at')
                ->comment('When tour was approved');

            $table->text('rejection_reason')
                ->nullable()
                ->after('approved_at')
                ->comment('Reason for rejection if status is rejected');

            // Add index for filtering pending approvals
            $table->index(['status', 'submitted_at'], 'idx_tours_approval_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex('idx_tours_approval_status');

            // Drop foreign keys
            $table->dropForeign(['created_by_operator_user_id']);
            $table->dropForeign(['approved_by_admin_id']);

            // Drop columns
            $table->dropColumn([
                'created_by_operator_user_id',
                'approved_by_admin_id',
                'submitted_at',
                'approved_at',
                'rejection_reason',
            ]);

            // Restore original status enum
            $table->enum('status', [
                'active',
                'inactive',
                'suspended',
                'archived'
            ])->default('active')->change();
        });
    }
};
