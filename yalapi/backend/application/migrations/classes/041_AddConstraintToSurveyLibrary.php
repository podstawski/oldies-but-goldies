<?php

class AddConstraintToSurveyLibrary extends Doctrine_Migration_Base
{
    public function up()
    {

        Doctrine_Manager::connection()->execute('ALTER TABLE "surveys_library" ADD CONSTRAINT "surveys_fk" FOREIGN KEY ("survey_id") REFERENCES "public"."surveys"("id")  ON DELETE CASCADE');
        
        Doctrine_Manager::connection()->execute('DROP INDEX IF EXISTS "unique_survey_id"');
        Doctrine_Manager::connection()->execute('CREATE UNIQUE INDEX "unique_survey_id" ON "surveys_library" USING BTREE ("survey_id")');


    }

    public function down()
    {
        Doctrine_Manager::connection()->execute('ALTER TABLE "surveys_library" DROP CONSTRAINT "surveys_fk";');
    }
}





