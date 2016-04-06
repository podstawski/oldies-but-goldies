<?php
class ImportFixes extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->dropForeignKey('skills', 'fk_skills_competencies');
        $this->removeColumn('skills', 'competence_id');
        $this->dropForeignKey('competencies', 'fk_competencies_projects');
        $this->removeColumn('competencies', 'project_id');
        $this->addColumn('competencies', 'md5id', 'character varying(32)', null, array('notnull' => true));
        $this->addColumn('competencies', 'md5skill_id', 'character varying(32)', null, array('notnull' => false));
        $this->addColumn('skills', 'md5group', 'character varying(32)', null, array('notnull' => true));
		$this->createTable('project_competencies', array
		(
			'id' => array
			(
				'type' => 'integer',
				'notnull' => true,
				'primary' => true,
				'autoincrement' => true,
			),
			'project_id' => array
			(
				'type' => 'Integer',
				'notnull' => true,
			),
			'competence_id' => array
			(
				'type' => 'Integer',
				'notnull' => true,
			),
		));

		$this->createForeignKey('project_competencies', 'fk_project_competencies_projects', array
		(
			'local'         => 'project_id',
			'foreign'       => 'id',
			'foreignTable'  => 'projects',
			'onDelete'      => 'CASCADE',
			'onUpdate'      => 'CASCADE'
		));
		$this->createForeignKey('project_competencies', 'fk_project_competencies_competencies', array
		(
			'local'         => 'competence_id',
			'foreign'       => 'id',
			'foreignTable'  => 'competencies',
			'onDelete'      => 'CASCADE',
			'onUpdate'      => 'CASCADE'
		));

    }

    public function down()
    {
        $this->addColumn('skills', 'competencies', 'int', null, array('notnull' => true));
        $this->createForeignKey('skills', 'fk_skills_competencies', array(
             'local'         => 'competence_id',
             'foreign'       => 'id',
             'foreignTable'  => 'competencies',
             'onDelete'      => 'CASCADE',
             'onUpdate'      => 'CASCADE'
        ));
        $this->addColumn('competencies', 'project_id', 'int', null, array('notnull' => true));
        $this->createForeignKey('competencies', 'fk_competencies_projects', array(
             'local'         => 'project_id',
             'foreign'       => 'id',
             'foreignTable'  => 'projects',
             'onDelete'      => 'CASCADE',
             'onUpdate'      => 'CASCADE'
        ));
        $this->removeColumn('skills', 'md5group');
        $this->removeColumn('competencies', 'md5id');
        $this->removeColumn('competencies', 'md5skill_id');
		$this->dropForeignKey('project_competencies', 'fk_project_competencies_projects');
		$this->dropForeignKey('project_competencies', 'fk_project_competencies_competencies');
		$this->dropTable('project_competencies');
    }
}


