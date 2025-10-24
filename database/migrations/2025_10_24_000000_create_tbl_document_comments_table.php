<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblDocumentCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_document_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('application_personnel_id');
            $table->string('document_type');
            $table->text('comment');
            $table->boolean('is_bad');
            $table->timestamps();

            $table->foreign('application_personnel_id')
                  ->references('application_personnel_id')
                  ->on('tbl_application_personnel')
                  ->onDelete('cascade');
        });

        // Add status columns to tbl_application_personnel
        Schema::table('tbl_application_personnel', function (Blueprint $table) {
            $table->string('application_letter_status')->default('pending');
            $table->string('cert_of_reg_status')->default('pending');
            $table->string('grade_slip_status')->default('pending');
            $table->string('brgy_indigency_status')->default('pending');
            $table->string('student_id_status')->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_document_comments');

        Schema::table('tbl_application_personnel', function (Blueprint $table) {
            $table->dropColumn([
                'application_letter_status',
                'cert_of_reg_status',
                'grade_slip_status',
                'brgy_indigency_status',
                'student_id_status'
            ]);
        });
    }
}