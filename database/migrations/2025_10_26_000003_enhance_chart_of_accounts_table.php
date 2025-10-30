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
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            // Add new columns
            $table->decimal('current_balance', 15, 2)->default(0)->after('opening_balance')->comment('Cached balance for performance');
            $table->timestamp('balance_updated_at')->nullable()->after('current_balance')->comment('Last time balance was calculated');
            $table->json('metadata')->nullable()->after('description')->comment('Additional account metadata');
            $table->string('created_by')->nullable()->after('updated_at')->comment('User who created the account');
            $table->string('updated_by')->nullable()->after('created_by')->comment('User who last updated the account');
        });

        // Add indexes for performance
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            // Composite index for account code and active status
            $table->index(['account_code', 'is_active'], 'idx_chart_accounts_code_active');

            // Index for account type and active status
            $table->index(['account_type', 'is_active'], 'idx_chart_accounts_type_active');

            // Index for parent and level (for hierarchy queries)
            $table->index(['parent_id', 'level'], 'idx_chart_accounts_parent_level');

            // Index for balance updated timestamp
            $table->index('balance_updated_at', 'idx_chart_accounts_balance_updated');

            // Index for created_by and updated_by
            $table->index('created_by', 'idx_chart_accounts_created_by');
            $table->index('updated_by', 'idx_chart_accounts_updated_by');
        });

        // Add full-text index for account name (MySQL specific)
        if (config('database.default') === 'mysql') {
            DB::statement('ALTER TABLE chart_of_accounts ADD FULLTEXT idx_chart_accounts_name_fulltext (account_name)');
        }

        // Add check constraints for data integrity
        DB::statement('ALTER TABLE chart_of_accounts ADD CONSTRAINT check_level_range CHECK (level >= 1 AND level <= 5)');
        DB::statement('ALTER TABLE chart_of_accounts ADD CONSTRAINT check_opening_balance CHECK (opening_balance >= 0)');
        DB::statement('ALTER TABLE chart_of_accounts ADD CONSTRAINT check_current_balance CHECK (current_balance >= 0)');

        // Add trigger to prevent circular references
        $this->createCircularReferenceTrigger();

        // Add trigger to update balance timestamp
        $this->createBalanceUpdateTrigger();

        // Add trigger to set created_by and updated_by
        $this->createAuditTriggers();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop triggers first
        $this->dropCircularReferenceTrigger();
        $this->dropBalanceUpdateTrigger();
        $this->dropAuditTriggers();

        // Drop indexes
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->dropIndex('idx_chart_accounts_code_active');
            $table->dropIndex('idx_chart_accounts_type_active');
            $table->dropIndex('idx_chart_accounts_parent_level');
            $table->dropIndex('idx_chart_accounts_balance_updated');
            $table->dropIndex('idx_chart_accounts_created_by');
            $table->dropIndex('idx_chart_accounts_updated_by');
        });

        // Drop full-text index (MySQL specific)
        if (config('database.default') === 'mysql') {
            DB::statement('ALTER TABLE chart_of_accounts DROP INDEX idx_chart_accounts_name_fulltext');
        }

        // Drop check constraints
        DB::statement('ALTER TABLE chart_of_accounts DROP CONSTRAINT check_level_range');
        DB::statement('ALTER TABLE chart_of_accounts DROP CONSTRAINT check_opening_balance');
        DB::statement('ALTER TABLE chart_of_accounts DROP CONSTRAINT check_current_balance');

        // Drop columns
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->dropColumn(['current_balance', 'balance_updated_at', 'metadata', 'created_by', 'updated_by']);
        });
    }

    /**
     * Create trigger to prevent circular references
     */
    private function createCircularReferenceTrigger(): void
    {
        $triggerName = 'check_chart_of_accounts_circular_reference';

        // Drop trigger if exists
        $this->dropCircularReferenceTrigger();

        $sql = "
            CREATE TRIGGER {$triggerName}
            BEFORE INSERT ON chart_of_accounts
            FOR EACH ROW
            BEGIN
                DECLARE ancestor_id INT;
                DECLARE ancestor_code VARCHAR(20);

                SET ancestor_id = NEW.parent_id;

                WHILE ancestor_id IS NOT NULL DO
                    SELECT account_code INTO ancestor_code FROM chart_of_accounts WHERE id = ancestor_id;

                    IF ancestor_id = NEW.id THEN
                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Circular reference detected in account hierarchy';
                    END IF;

                    SELECT parent_id INTO ancestor_id FROM chart_of_accounts WHERE id = ancestor_id;
                END WHILE;
            END
        ";

        DB::statement($sql);

        // Create similar trigger for UPDATE
        $updateTriggerName = 'check_chart_of_accounts_circular_reference_update';
        $updateSql = "
            CREATE TRIGGER {$updateTriggerName}
            BEFORE UPDATE ON chart_of_accounts
            FOR EACH ROW
            BEGIN
                DECLARE ancestor_id INT;
                DECLARE ancestor_code VARCHAR(20);

                IF NEW.parent_id IS NOT NULL AND NEW.parent_id != OLD.parent_id THEN
                    SET ancestor_id = NEW.parent_id;

                    WHILE ancestor_id IS NOT NULL DO
                        SELECT account_code INTO ancestor_code FROM chart_of_accounts WHERE id = ancestor_id;

                        IF ancestor_id = NEW.id THEN
                            SIGNAL SQLSTATE '45000'
                            SET MESSAGE_TEXT = 'Circular reference detected in account hierarchy';
                        END IF;

                        SELECT parent_id INTO ancestor_id FROM chart_of_accounts WHERE id = ancestor_id;
                    END WHILE;
                END IF;
            END
        ";

        DB::statement($updateSql);
    }

    /**
     * Drop circular reference trigger
     */
    private function dropCircularReferenceTrigger(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS check_chart_of_accounts_circular_reference');
        DB::statement('DROP TRIGGER IF EXISTS check_chart_of_accounts_circular_reference_update');
    }

    /**
     * Create trigger to update balance timestamp
     */
    private function createBalanceUpdateTrigger(): void
    {
        $triggerName = 'update_chart_of_accounts_balance_timestamp';

        // Drop trigger if exists
        DB::statement('DROP TRIGGER IF EXISTS ' . $triggerName);

        $sql = "
            CREATE TRIGGER {$triggerName}
            BEFORE UPDATE ON chart_of_accounts
            FOR EACH ROW
            BEGIN
                IF NEW.opening_balance != OLD.opening_balance THEN
                    SET NEW.balance_updated_at = CURRENT_TIMESTAMP();
                END IF;
            END
        ";

        DB::statement($sql);
    }

    /**
     * Drop balance update trigger
     */
    private function dropBalanceUpdateTrigger(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS update_chart_of_accounts_balance_timestamp');
    }

    /**
     * Create triggers for audit fields
     */
    private function createAuditTriggers(): void
    {
        // Create trigger for INSERT
        $insertTriggerName = 'set_chart_of_accounts_created_by';
        DB::statement('DROP TRIGGER IF EXISTS ' . $insertTriggerName);

        $insertSql = "
            CREATE TRIGGER {$insertTriggerName}
            BEFORE INSERT ON chart_of_accounts
            FOR EACH ROW
            BEGIN
                SET NEW.created_by = COALESCE(@current_user_id, 'system');
                SET NEW.updated_by = COALESCE(@current_user_id, 'system');
            END
        ";

        DB::statement($insertSql);

        // Create trigger for UPDATE
        $updateTriggerName = 'set_chart_of_accounts_updated_by';
        DB::statement('DROP TRIGGER IF EXISTS ' . $updateTriggerName);

        $updateSql = "
            CREATE TRIGGER {$updateTriggerName}
            BEFORE UPDATE ON chart_of_accounts
            FOR EACH ROW
            BEGIN
                SET NEW.updated_by = COALESCE(@current_user_id, 'system');
            END
        ";

        DB::statement($updateSql);
    }

    /**
     * Drop audit triggers
     */
    private function dropAuditTriggers(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS set_chart_of_accounts_created_by');
        DB::statement('DROP TRIGGER IF EXISTS set_chart_of_accounts_updated_by');
    }
};
