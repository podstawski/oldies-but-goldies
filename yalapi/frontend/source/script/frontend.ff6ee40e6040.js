qx.Class.define("Tools",
{
    extend : qx.core.Object,

    members :
    {

    },
    
    statics :
    {
        tr : function(messageId, varargs)
        {
            var localeManager = qx.locale.Manager;
            return localeManager.tr.apply(localeManager, arguments);
        },

        dimensions: function(percentH, percentW, windowContext)
        {
            return {
                h: Math.round(window.innerHeight  * percentH),
                w: Math.round(window.innerWidth  * percentW)
            };
        },

        pad : function(number) { return (number < 10 ? '0' : '') + number; },

        loadAdditionalTranslations : function()
        {
            var localeManager = qx.locale.Manager.getInstance();
            for (var languageCode in this.__lang) {
                localeManager.addTranslation(languageCode, this.__lang[languageCode]);
            }

            localeManager.addLocale("pl", {
                "cldr_number_decimal_separator" : "."
            });
        },
        __lang :
        {
            "pl" :
            {
                "Lang:pl" : "Polski",
                "Lang:en" : "Angielski",

                "edit"  : "Edytuj",
                "add"   : "Dodaj",
                "setLessons" : "Rozplanuj",
                "generateReport" : "Raport",
                "close" : "Zamknij",

                //errors
                'error.user_exists'             : 'Taka nazwa użytkownika już istnieje',

                "This field is required"        : "To pole jest wymagane",
                "form.file:browse"              : "Przeglądaj",

                "_button_:edit"                 : "edytuj",
                "_button_:remove"               : "usuń",
                "_button_:print"                : "drukuj",
                "_button_:send"                 : "roześlij",

                "mailbox.list:sender"           : "Nadawca",
                "mailbox.list:subject"          : "Temat",
                "mailbox.list:date"             : "Data nadania",

                "mailbox.users.tab:groups"      : "Grupa",
                "mailbox.users.tab:users"       : "Uczestnicy",
                "mailbox.users.tab:trainers"    : "Trenerzy",
                "mailbox.users.tab:all"         : "Wszyscy",

                "calendar.addcoach.addcoach"        : "Dodaj trenera",
                "calendar.addroom.addroom"          : "Dodaj pomieszczenie",
                "calendar.menu.button:back"         : "Powrót do menu głównego",
                "calendar.menu.button:add-lesson"   : "Dodaj wydarzenie",
                "calendar.menu.chooseDate"      : "Wybierz datę:",
                "calendar.menu.centers"         : "Ośrodki:",
                "calendar.menu.rooms"           : "Pomieszczenia:",
                "calendar.menu.coaches"         : "Trenerzy:",

                "calendar:course-description"           : "Nazwa szkolenia: ",
                "calendar:course-units-description"     : "Jednostki lekcyjne: ",
                "calendar:groupbox-description"         : "Dane o szkoleniu",
                "calendar.menu:rooms"                   : " - pomieszczenia:",
                "calendar.menu:rooms-add"               : " Dodaj pomieszczenie:",
                "calendar.menu:coaches"                 : "Trenerzy: ",
                "calendar.menu:coaches-add"             : "Dodaj trenera: ",
                "calendar.in-courses.button:set-lesson" : "Dodaj lekcje",

                "calendar.init:room"            : "Pomieszczenie:",
                "calendar.init:when"            : "Data:",
                "calendar.init:what"            : "Lekcja:",
                "calendar.init:recurring"       : "Powtarzaj:",

                "calendar.init:tooltip.date_s"  : "Data początkowa: ",
                "calendar.init:tooltip.date_e"  : "Data końcowa: ",
                "calendar.init:tooltip.coach"   : "Trener: ",
                "calendar.init:tooltip.event"   : "Opis lekcji: ",
                "calendar.init:lesson same room" : "Nie można dodać tej lekcji. W tej sali odbywają sie już inne zajęcia!",
                "calendar.lesson:hour_amount_limit" : "Nie można dodac więcej lekcji z tej jednostki lekcyjnej!",
                "calendar.lesson:lesson_added" : "Lekcja dodana",
                "calendar.init.lesson:deleted"  : "Usunięto lekcję",

                "calendar.lesson.collisions:found"       : "Znaleziono następujące kolizje przy dodawaniu lekcji: ",
                "calendar.lesson.collisions:room"        : "zajęcia w tym samym pomieszczeniu",
                "calendar.lesson.collisions:coach"       : "zajęcia z tym samym trenerem",
                "calendar.lesson.collisions:course_unit" : "zajęcia tej samej grupy",

                "course.list:id"                : "ID",
                "course.list:code"              : "Kod",
                "course.list:name"              : "Nazwa",
                "course.list:is_active"         : "Aktywny",
                "course.list:parent_name"       : "Kurs nadrzędny",
                "course.list:created_date"      : "Data utworzenia",
                "course.list:actions"           : " ",

                "course.list.tab:current"       : "Bieżące",
                "course.list.tab:upcoming"      : "Planowane",
                "course.list.tab:arch"          : "Archiwalne",

                "project.list.tab:current"      : "Bieżące",
                "project.list.tab:upcoming"     : "Planowane",
                "project.list.tab:arch"         : "Archiwalne",

                "form.course:name"                 : "Nazwa szkolenia",
                "form.course:code"                 : "Kod",
                "form.course:color"                : "Kolor",
                "form.course:level"                : "Poziom",
                "form.course:project_id"           : "Projekt",
                "form.course:price"                : "Cena szkolenia",
                "form.course:status"               : "Status",
                "form.course:training_center_id"   : "Ośrodek",
                "form.course:description"          : "Opis szkolenia",
                "form.course:group_id"             : "Grupa szkoleniowa",

                "form.course:button cancel"        : "Anuluj",
                "form.course:button add"           : "Dodaj szkolenie",
                "form.course:button edit"          : "Zapisz zmiany",
                "form.course:window add"           : "Dodawanie nowego szkolenia",
                "form.course:window edit"          : "Edytowanie informacji o szkoleniu",
                "form.course:added"                : "Szkolenie zostało dodane!",
                "form.course:edited"               : "Zmiany zostały zapisane",

                "survey.addquestion:title"     : "Treść",
                "survey.addquestion:help"      : "Tekst pomocniczy",
                "survey.addquestion:type"      : "Typ pytania",
                "survey.addquestion:name"      : "Treść",
                "survey.addquestion:required"  : "Wymagane",
                "survey.create:save"           : "Zapisz",
                "survey.create:send"           : "Wyślij",
                "survey.create:cancel"         : "Anuluj",

                "survey.sent"                  : "Ankieta wysłana",
                "test.sent"                    : "Test wysłany",

                "survey.create:name"           : "Nazwa",
                "survey.create:type"           : "Typ",
                "survey.create:question"       : "Pytanie",

                "survey.create:no_name"        : "Ankieta bez nazwy",
                "test.create:no_name"          : "Test bez nazwy",
                "survey.create:description"    : "Opis",
                "survey.create:description-placeholder"    : "Możesz wpisać tutaj dowolną informację, bądź tekst który pomoże w wypełnieniu ankiety.",
                "test.create:description-placeholder"    : "Możesz wpisać tutaj dowolną informację, bądź tekst który pomoże w wypełnieniu testu.",
                "survey.create:type_survey"    : "Ankieta",
                "survey.create:type_test"      : "Test",
                "survey.create:add_question"   : "Dodaj pytanie",
                "survey.create:user_answer"    : "Odpowiedź użytkownika",
                "survey.create:answer"         : "Odpowiedź",
                "survey.create:add_answer"     : "Dodaj odpowiedź",
                "survey.list:add new"          : "Dodaj nową ankietę",

                "survey.list:confirm removal"  : "Czy jesteś pewien, że chcesz usunąć ankietę?",
                "survey.list:quiz was removed" : "Ankieta została usunięta",
                "survey.list.tab:new"          : "Nowe",
                "survey.list.tab:completed"    : "Zakończone",
                "survey.results:close"         : "Zamknij",
                "quiz.create:title"            : "Tytuł",

                "fillSurvey"                   : "Wypełnij",
                "surveyAddToLibrary"           : "Biblioteka",
                "details"                      : "Szczegóły",
                "detailedResultsSurvey"        : "Wyniki szczegółowe",
                "summary"                      : "Podsumowanie",
                "detailedResults"              : "Wyniki szczegółowe",
                "averageResults"               : "Uśrednione wyniki",
                "sendSurvey"                   : "Wyślij",
                "finishSurvey"                 : "Zakończ",
                "copyToMySurveys"              : "Kopiuj do moich ankiet",
                "copyToMyTests"                : "Kopiuj do moich testów",
                "delete"                       : "Usuń",
                "surveyResults"                : "Wyniki",
                "archiveSurvey"                : "Archiwum",
                "dearchiveSurvey"              : "Usuń (tylko z archiwum)",
                "surveys.completed"            : "Zakończona",
                "resumeSurvey"                 : "Wznów",
                "survey.filled"                : "Ankieta wypełniona",
                "test.filled"                  : "Test wypełniony",
                "surveys.tab.awaiting"         : "Oczekujące",
                "surveys.tab.filled"           : "Wypełnione",
                "surveys.type.survey"          : "Ankieta",
                "surveys.type.test"            : "Test",
                "surveys.type.type"            : "Typ",
                "surveys.type.description"     : "Opis",
                "surveys.type.deadline"        : "Termin wypełnienia",
                "surveys.type.created"         : "Dodano",
                "surveys.type.created_by"      : "Przez",
                "surveys.tab.archive"          : "Archiwum",
                "surveys.tab.my_surveys"       : "Moje ankiety",
                "surveys.tab.my_tests"         : "Moje testy",
                "surveys.new"                  : "Nowa ankieta",
                "tests.new"                    : "Nowy test",
                "test.send"                    : "Test został wysłany",
                "surveys.edit"                 : "Edycja ankiety",
                "tests.edit"                   : "Edycja testu",
                "surveys.tab.survey_library"   : "Biblioteka",
                "surveys.list.average"         : "Średnia",
                "surveys.list.replies_count"   : "Wypełniono razy",
                "surveys.list.nodata"          : "Brak danych",
                "surveys.list.advance_level"   : "Poziom zaawansowania",
                "surveys.tab.results"          : "Wyniki",
                "surveys.list.unfilled"        : "NIEWYPEŁNIONE",
                "surveys.tab.group_results"    : "Wyniki grupy",
                "form.survey.group.select:caption" : "Wybierz grupy",
                "form.group.add.buttonSend"    : "Wyślij",
                "form.survey.send:caption": "Wyślij do grup szkoleniowych",
                "quiz.create:file"             : "Plik",
                "quiz.create:description"      : "Opis",
                "quiz.create:submit"           : "Zapisz",
                "quiz.create:url"              : "Adres",
                "quiz.edit:title"              : "Tytuł",

                "quiz.edit:file"               : "Plik",
                "quiz.edit:description"        : "Opis",
                "quiz.edit:submit"             : "Zapisz",
                "quiz.edit:url"                : "Adres",
                "quiz.edit:changes saved"      : "Zmiany zostały zapisane",
                "quiz.list.tab:new"            : "Nowe",

                "quiz.list.tab:completed"      : "Wypełnione",
                "quiz.list.tab:all"            : "Wszystkie",
                "quiz.list:search"             : "Wyszukaj...",
                "quiz.list:confirm removal"    : "Potwierdź usunięcie",
                "quiz.list:quiz was removed"   : "Quiz został usunięty",
                "quiz.list:add new"            : "Dodaj nowy quiz",
                "_previewImageIcon_:go-previous"    : "Poprzedni",

                "_previewImageIcon_:go-next"        : "Następny",
                "_previewImageIcon_:zoom-in"        : "Powiększ",
                "_previewImageIcon_:edit-delete"    : "Usuń",
                "quiz.addquiz:name"             : "Nazwa quizu",

                "form.quiz:name"             : "Nazwa quizu",
                "form.quiz:description"      : "Opis",
                "form.quiz:time_limit"       : "Limit czasowy (sekundy??)",
                "form.quiz:url"              : "Adres WWW",
                "form.quiz:button cancel"    : "Anuluj",
                "form.quiz:button add"       : "Dodaj quiz",
                "form.quiz:button edit"      : "Zapisz zmiany",
                "form.quiz:window add"       : "Dodawanie nowego quizu",
                "form.quiz:window edit"      : "Edytowanie danych o quizie",
                "form.quiz:added"            : "Dodano nowy quiz!",
                "form.quiz:edited"           : "Zmiany zostały zapisane",

                "form.training_center.add:name"              : 'Nazwa ośrodka szkoleniowego',
                "form.training_center.add:street"            : 'Ulica',
                "form.training_center.add:zip_code"          : 'Kod pocztowy',
                "form.training_center.add:city"              : 'Miasto',
                "form.training_center.add:caption"           : 'Dodaj ośrodek szkoleniowy',
                "form.training_center.add:success"           : 'Dodano ośrodek szkoleniowy',
                "form.training_center.add:manager"           : 'Kierownik',
                "form.training_center.add:url"               : 'Strona internetowa',
                "form.training_center.add:room_amount"       : 'Liczba sal',
                "form.training_center.add:seats_amount"      : 'Liczba miejsc',
                "form.training_center.add:button add"        : 'Dodaj ośrodek',
                "form.training_center.add:window add"        : 'Dodawanie nowego ośrodka szkoleniowego',
                "form.training_center.add:button edit"       : 'Zapisz zmiany',
                "form.training_center.add:window edit"       : 'Edytowanie informacji o ośrodku szkoleniowym',
                "form.training_center.add:button cancel"     : 'Anuluj',
                "form.training_center.add:added"             : 'Dodano ośrodek szkoleniowy!',
                "form.training_center.add:edited"            : 'Zmiany zostały zapisane',

                "form.lesson:courses"           : "Szkolenie",
                "form.lesson:course_units"      : "Jednostka szkoleniowa",
                "form.lesson:coaches"           : "Trener",
                "form.lesson:groups"            : "Grupa szkoleniowa",
                "form.lesson:rooms"             : "Sala",
                "form.lesson:buttonSave"        : "Zapisz",
                "form.lesson:buttonCancel"      : "Anuluj",
                "form.lesson:date"              : "Data",
                "form.lesson:caption"           : "Dodaj lekcję",

                "form.lesson:hourStart"       : "Godzina rozpoczecia",
                "form.lesson:hourEnd"     : "Godzina zakończenia",

                "form.training_center.add:pageGeneral"       : 'Ogólne',
                "form.training_center.add:pageResources"     : 'Zasoby',
                "form.training_center.add:pageRooms"         : 'Sale',

                "form.training_center.add.label:NO"          : "Lp.",
                "form.training_center.add.label:name"        : "Nazwa",
                "form.training_center.add.label:quantity"    : "Ilość",

                "form.training_center.add.button:AddResource"       : "Dodaj zasób",
                "form.training_center.add.button:Cancel"            : "Anuluj",
                "form.training_center.add.button:Save"              : "Zapisz",

                "form.project:name"                 : 'Nazwa projektu',
                "form.project:code"                 : 'Kod projektu',
                "form.project:description"          : 'Cel projektu',
                "form.project:status"               : 'Status projektu',
                "form.project:success"              : 'Dodano projekt',
                "form.project:start_date"           : 'Start projektu',
                "form.project:end_date"             : 'Koniec projektu',
                "form.project:button add"           : 'Dodaj projekt',
                "form.project:window add"           : 'Dodawanie nowego projektu',
                "form.project:button edit"          : 'Zapisz zmiany',
                "form.project:window edit"          : 'Edytowanie danych o szkoleniu',
                "form.project:button cancel"        : 'Anuluj',
                "form.project:added"                : 'Projekt został dodany!',
                "form.project:edited"               : 'Zmiany zostały zapisane',
                
                "form.user.add.groupbox.usersInfo:caption"      : "Dane o grupie:",
                "form.user.add.groupbox.usersInfo:name"         : "Nazwa grupy:",
                "form.user.add.groupbox.usersInfo:level"        : "Poziom zaawansowania:",
                "form.user.add.groupbox.users:caption"          : "Użytkownicy:",
                "form.user.add.groupbox.usersGroups:caption"    : "Użytkownicy należący do grupy:",
                "form.user.add:message:select-level"            : "Wybierz poziom zaawansowania!",
                "form.user.add.message:group-added"             : "Grupa dodana poprawnie",
                "form.user.add.message:group-and-users-added"   : "Dodano poprawnie grupę oraz jej uczestników",
                "form.user.add:caption"                         : "Dodaj szkolenie",
                "form.user.add.button.add"                      : "Dodaj użytkowników do grupy szkoleniowej",
                "form.user.add.button.delete"                   : "Usuń użytkowników z grupy szkoleniowej",
                "form.user.add.button.cancel"                   : "Anuluj",
                "form.user.add.button.save"                     : "Utwórz grupę szkoleniową",

                "form.user:window caption"              : 'Dodaj/edytuj użytkownika',
                "form.user:username"                    : 'Nazwa użytkownika',
                "form.user:first_name"                  : 'Imię',
                "form.user:last_name"                   : 'Nazwisko',
                "form.user:plain_password"              : 'Hasło',
                "form.user:email"                       : 'E-mail',
                "form.user:role_id"                     : 'Rola',
                "form.user:button cancel"               : 'Anuluj',
                "form.user:button add"                  : 'Zapisz',
                "form.user:button edit"                 : 'Zapisz',
                "form.user.error.invalid_username"      : 'Nazwa użytkownika może zawierać tylko litery, cyfry oraz znak "_"',

                "lib.grid.Abstract.delete-record-confirm"       : "'Na pewno chcesz usunać dany rekord?'",
                "lib.grid.Abstract.delete-records-confirm"      : "'Na pewno chcesz usunać wskazane rekordy?'",

                "form.group.add:caption"                        : 'Tworzenie grupy szkoleniowej',
                "form.group.add.groupbox.usersInfo:caption"     : 'Informacje o grupie szkoleniowej',
                "form.group.add.groupbox.usersInfo:name"        : 'Nazwa grupy',
                "form.group.add.groupbox.usersInfo:level"       : 'Poziom zaawansowania',
                "form.group.add.groupbox.users:caption"         : 'Lista użytkowników',
                "form.group.add.groupbox.usersGroups:caption"   : 'Uczestnicy w grupie szkoleniowej',
                "form.group.add.buttonAdd"                     : 'Dodaj uczestników do grupy',
                "form.group.add.buttonDelete"                  : 'Usuń uczestników z grupy',
                "form.group.add.buttonSave"                    : 'Zapisz',
                "form.group.add.buttonCancel"                  : 'Anuluj',
                "form.group.add.message:select-level"          : 'Wybierz poziom grupy!',

                "form.group.edit.message:group-and-users-edited" : "Wyedytowano grupę",

                "form.resource_type:name"                       : 'Nazwa zasobu',
                "form.resource_type:button add"                 : 'Zapisz',
                "form.resource_type:button edit"                : 'Zapisz',
                "form.resource_type:button cancel"              : 'Anuluj',
                "form.resource_type.window"                     : 'Dodawanie/edycja zasobu',
                "form.resource_type:added"                      : 'Dodano zasób',
                "form.resource_type:edited"                     : 'Zapisano zmiany',

                "report.list.tab:general"           : "Ogólne",
                "report.list.tab:my"                : "Własne",
                "form.report.copy:window"           : "Skopiuj do projektu",
                "form.report.copy:project_id"       : "Nazwa projektu",
                "form.report.copy:button cancel"    : "Anuluj",
                "form.report.copy:button add"       : "Skopiuj",
                "form.report.copy:added"            : "Skopiowano raport do projektu",
                "form.report.edit:window"           : 'Edycja danych i pliku raportu',
                "form.report.edit:name"             : 'Nazwa raportu',
                "form.report.edit:description"      : 'Opis raportu',
                "form.report.edit:template_file"    : 'Własny plik z raportem',
                "form.report.edit:button cancel"    : "Anuluj",
                "form.report.edit:button edit"      : "Zapisz",
                "form.report.edit:edited"           : "Zapisano dane",

                "copyToProject"                     : "Kopiuj do projektu",
                "download"                          : "Pobierz wzór",

                "login:username" : "Nazwa użytkownika",
                "login:password" : "Hasło",
                "login:submit"   : "Zaloguj",

                "form.exam:name"            : "nazwa",
                "form.exam:type"            : "typ",
                "form.exam:created_date"    : "data",
                "form.exam:button cancel"   : "Anuluj",
                "form.exam:button add"      : "Dodaj sprawdzian",
                "form.exam:button edit"     : "Zapisz zmiany",
                "form.exam:window add"      : "Dodawanie nowego sprawdzianu",
                "form.exam:window edit"     : "Edytowanie informacji o sprawdzianie",
                "form.exam:added"           : "Dodano nowy sprawdzian!",
                "form.exam:edited"          : "Zmiany zostały zapisane!",

                "form.room:name"                : 'Nazwa sali',
                "form.room:symbol"              : 'Symbol',
                "form.room:description"         : 'Opis',
                "form.room:available_space"     : 'Dostępne miejsce',
                "form.room:training_center_id"  : 'Przynależy do ośrodka',
                "form.room:button cancel"       : 'Anuluj',
                "form.room:button add"          : 'Zapisz',
                "form.room:window add"          : 'Dodawanie/edycja sali',
                "form.room:window edit"         : 'Dodawanie/edycja sali',

                "table.presence:coach_name"  : "Trener",
                "table.presence:course_name" : "Szkolenie",
                "table.presence:unit_name"   : "Jednostka",
                "table.presence:tc_name"     : "Ośrodek",
                "table.presence:tc_adress"   : "Adres",
                "table.presence:room_name"   : "Sala",
                "table.presence:start_date"  : "Początek",
                "table.presence:end_date"    : "Zakończenie",

                "form.course_schedule:subject"       : "temat",
                "form.course_schedule:schedule"      : "opis",
                "form.course_schedule:button edit"   : "Zapisz zmiany",
                "form.course_schedule:button cancel" : "Anuluj",
                "form.course_schedule:window edit"   : "Edytowanie informacji o przebiegu zajęć",

                "form.report_picker:window"             : 'Generowanie dokumentu',
                "form.report_picker:name"               : 'Typ dokumentu',
                "form.report_picker:generating_report"  : 'Proszę czekać...',
                "form.report_picker:button cancel"      : 'Zamknij',
                "form.report_picker:button add"         : 'Wygeneruj',

                "reportPicker.template.GroupList"       : 'Lista uczestnikówo grupy',
                
                "mailbox:folder1" : "Skrzynka odbiorcza",
                "mailbox:folder2" : "Skrzynka nadawcza",
                "mailbox:folder3" : "Kosz",

                "addressbook.tab:groups"    : "Grupy",
                "addressbook.tab:users"     : "Użytkownicy",
                "addressbook.tab:trainers"  : "Trenerzy",

                "form.compose:recipient_list"   : "Odbiorca",
                "form.compose:subject"          : "Temat",
                "form.compose:attachment"       : "Załącznik",
                "form.compose:window add"       : "Tworzenie nowej wiadomości"
            },
            "en" :
            {
                "Lang:pl" : "Polish",
                "Lang:en" : "English"
            }
        },
        rand : function(min, max)
        {
            var argc = arguments.length;
            if (argc === 0) {
                min = 0;
                max = Number.MAX_VALUE;
            } else if (argc === 1) {
                throw new Error('Warning: rand() expects exactly 2 parameters, 1 given');
            }
            return Math.floor(Math.random() * (max - min + 1)) + min;
        },
        drand : function(min, max)
        {
            return parseFloat(this.rand(min, max) + "." + this.rand(0, 100));
        },
        calendar :
        {
            pl :
            {
                date:
                {
                    month_full:
                    [
                        "Styczeń", "Luty", "Marzec", "Kwiecień", "Maj", "Czerwiec",
                        "Lipiec", "Sierpień", "Wrzesień", "Październik", "Listopad", "Grudzień" ],
                    
                    month_short :   ["Sty", "Lut", "Mar", "Kwi", "Maj", "Cze", "Lipl", "Sie", "Wrz", "Paź", "Lis", "Gru"],
                    day_full    :   ["Niedziela", "Poniedziałek", "Wtorek", "Środa", "Czwartek", "Piątek", "Sobota"],
                    day_short:["Nd", "Pon", "Wt", "Śr", "Czw", "Pt", "Sb"]
                },
                labels:
                {
                    dhx_cal_today_button    :"Dzisiaj",
                    day_tab                 :"Dzień",
                    week_tab                :"Tydzień",
                    month_tab               :"Miesiąc",
                    new_event               :"Nowa lekcja",
                    icon_save               :"Zapisz",
                    icon_cancel             :"Anuluj",
                    icon_details            :"Szczegóły",
                    icon_edit               :"Edytuj",
                    icon_delete             :"Usuń",
                    confirm_closing         :"",
                    confirm_deleting        :"",
                    section_description     :"Opis",
                    section_time            :"Przedział czasowy"
                },
                tooltip:
                {
                    room  : "Pomieszczenie:",
                    coach : "Trener:",
                    event : "Wydarzenie:",
                    date_s: "Data początkowa:",
                    date_e: "Data końcowa:"
                }

            }
        },
        findClass : function(classToFind)
        {
            var found = false, className;
            ["frontend", "qx"].forEach(function(namespace){
                if (found === false) {
                    className = namespace + "." + classToFind;
                    if (qx.Class.isDefined(className)) {
                        found = className;
                    }
                }
            });
            return found;
        }
    }
});
