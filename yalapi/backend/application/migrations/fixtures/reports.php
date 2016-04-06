<?php

//requires projects.php

return array(
    //table column names
    array('parent_id', 'name', 'path', 'project_id', 'description'),
    //values
    array(null, 'Lista obecności uczestników szkolenia', 'presence_list.jrxml', null, 'Generuje listę uczestników danej grupy szkoleniowej'),
    array(null, 'Lista na drzwi', 'door_list.jrxml', null, 'Lista na drzwi'),
    array(null, 'Zaświadczenia', 'certificates.jrxml', null, 'Zaświadczenia'),
    array(null, 'Potwierdzenie odbioru zaświadczeń', 'certificates_receive_confirmation.jrxml', null, 'Potwierdzenie odbioru zaświadczeń'),
    array(null, 'Potwierdzenie odbioru loginów', 'logins_receive_confirmation.jrxml', null, 'Potwierdzenie odbioru loginów'),
    array(null, 'Potwierdzenie odbioru materiałów', 'training_materials_receive_confirmation.jrxml', null, 'Potwierdzenie odbioru materiałów'),
    array(null, 'Harmonogram szkolenia', 'course_schedule.jrxml', null, 'Harmonogram szkolenia'),
    array(null, 'Karta zgłoszeniowa', 'registration_form.jrxml', null, 'Karta zgłoszeniowa'),
    array(null, 'Wyniki ankiety/testu', 'survey_results.jrxml', null, 'Wyniki ankiety/testu'),
    array(null, 'Karta zgłoszeniowa nowego uczestnika', 'new_student.jrxml', null, 'Karta zgłoszeniowa'),
);
