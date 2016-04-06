<?php

class SurveyResult extends AclModel
{
    static $table_name = 'survey_results';

    static $belongs_to = array(
        array('survey')
    );

    static $after_save = 'RunAcl';
    
    public function RunAcl()
    {
        self::grant(Role::COACH,$this->survey->user_id,$this->id);
    }
   
    public static function calculateScore($id){
        $processedResults = array();

        $results = SurveyResult::all(array(
            'select'    => 'q.id as question_id, content, correct, type, answer_id, answer_content ',
            'from'      => 'survey_possible_answers spa',
            'joins'     => 'left join survey_questions q on q.id = spa.question_id
                            left join survey_results sr on sr.survey_id = q.survey_id
                            left join survey_detailed_results sdr on sdr.answer_id = spa.id',
            'group'     => 'answer_id, q.id , content, correct, type, answer_content',
            'conditions'=> array('sr.id=?', $id)
         ));

        //rewrite so one question has one array entry
        foreach($results as $row){
            $processedResults[$row->question_id][] = $row->attributes();
        }

        $totalScore = 0;
        $scorableQuestions = 0;
        
        foreach($processedResults as $item) {
            if (count($item) > 1){
                $sc = 0;
                $scorableQuestions++;

                foreach($item as $subitem) {
                    if ($subitem['correct'] == 0 && $subitem['answer_id']) {
                        $sc = 0;
                        break;
                    }else{
                        $sc = 1;
                    }
                }
                $totalScore += $sc;
            } else {
                if ($item[0]['answer_content'] != null) {
                    $scorableQuestions++;
                }

                $totalScore += $item[0]['correct'];
            }
        }

        return ($totalScore/ max($scorableQuestions,1)*100);
    }
}