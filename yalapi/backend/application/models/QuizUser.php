<?php

class QuizUser extends AclModel
{
    static $table_name = 'quiz_users';

    static $belongs_to = array(
        array('quiz'),
        array('user')
    );

    static $after_destroy = array('revoke_access');

    /**
     * @static
     * @param array $groups array with group ids
     * @param interger $quizId
     * @return $inserts array with inserted data
     */
    public static function insert_by_groups(array $groups, $quizId)
    {
        $inserts = array();

        foreach ($groups as $groupId) {
            $group = Group::first($groupId);
            foreach ($group->users as $user) {
                try {
                    $row = static::create($data = array('quiz_id' => $quizId, 'user_id' => $user->id));
                    if ($row === false) {
                        throw new RuntimeException('Error when saving quiz user group');
                    }
                    //RB grant acl access - it's here to ensure adding permission for each record separatelly
                    static::grant(Role::USER, $user->id, $quizId, 'quizzes');
                    $inserts[] = $data;

                } catch (ActiveRecord\DatabaseException $e) {
                    //RB if it's primary key violation, go further)
                    if ($e->getCode() !== 23505) {
                        throw $e;
                    }
                }
            }
        }
        return $inserts;
    }

    public function revoke_access()
    {
        self::revoke(Role::USER, $this->user_id, $this->quiz_id, 'quizzes');
    }
}
