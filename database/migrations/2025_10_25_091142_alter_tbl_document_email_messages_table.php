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
        Schema::table('tbl_document_email_messages', function (Blueprint $table) {
            // Check if the foreign key exists before dropping
            if (Schema::hasColumn('tbl_document_email_messages', 'document_comment_id')) {
                $table->dropForeign(['document_comment_id']);
                $table->dropColumn('document_comment_id');
            }
            
            // Drop columns if they exist
            if (Schema::hasColumn('tbl_document_email_messages', 'document_type')) {
                $table->dropColumn('document_type');
            }
            if (Schema::hasColumn('tbl_document_email_messages', 'email_message')) {
                $table->dropColumn('email_message');
            }

            // Add the new columns if they don't exist
            if (!Schema::hasColumn('tbl_document_email_messages', 'email_content')) {
                $table->text('email_content')->nullable();
            }
            if (!Schema::hasColumn('tbl_document_email_messages', 'sent_at')) {
                $table->timestamp('sent_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_document_email_messages', function (Blueprint $table) {
            // Rollback changes
            $table->unsignedBigInteger('document_comment_id');
            $table->string('document_type');
            $table->text('email_message');

            // Drop the newly added columns
            $table->dropColumn(['email_content', 'sent_at']);

            // Recreate the foreign key constraint
            $table->foreign('document_comment_id')
                  ->references('id')
                  ->on('tbl_document_comments')
                  ->onDelete('cascade');
        });
    }
};
