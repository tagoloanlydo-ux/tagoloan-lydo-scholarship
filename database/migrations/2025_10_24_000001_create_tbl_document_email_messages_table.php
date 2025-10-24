<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblDocumentEmailMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_document_email_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_comment_id');
            $table->unsignedBigInteger('application_personnel_id');
            $table->string('document_type');
            $table->text('email_message');
            $table->timestamps();

            $table->foreign('document_comment_id')
                  ->references('id')
                  ->on('tbl_document_comments')
                  ->onDelete('cascade');

            $table->foreign('application_personnel_id')
                  ->references('application_personnel_id')
                  ->on('tbl_application_personnel')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_document_email_messages');
    }
}