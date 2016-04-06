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

        pad : function(number)
        {
            return (number < 10 ? '0' : '') + number;
        },

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

                // validators errors
                "validate.messages.not_number" : "podana wartośc nie jest liczbą",
                "validate.messages.not_number_smaller_then" : "podano za dużą wartość",
                "validate.messages.date.null" : "brak podanej daty",

                "invalid pesel" : "Nieprawidłowy PESEL",
                "pesel must have %2 chars" : "PESEL musi składać się z %2 znaków",
                "pesel must contain only digits" : "PESEL musi składać się tylko z liczb",
                "value is not a number" : "Wartość musi być liczbą",
                "value is not a valid email address" : "Wartość nie jest prawidłowym adresem e-mail",
                "value is not a string" : "Wartość nie jest ciągiem znaków",
                "value is not an url" : "Wartość nie jest prawidłowym adresem URL",
                "value is not a valid color" : "Wartośc nie jest prawidłowym kolorem",
                "value is not in \"%2\"" : "Wartość nie zawiera się w \"%2\"",
                "value is not in the range from [%2, %3]" : "Wartość nie należy do przedziału [%2, %3]",
                "value has invalid format" : "Wartośc ma nieprawidłowy format",
                "value must consist of alphanumeric characters" : "Wartość musi składać się znaków alfanumerycznych",
                "value must be greater than %2" : "Wartość musi być większa od %2",
                "value must not be lower than %2" : "Wartość nie może być mniejsza niż %2",
                "value must be lower than %2" : "Wartość musi być mniejsza od %2",
                "value must not be greater than %2" : "Wartość nie może być większa od %2",
                "value must be between %2 and %3 characters long" : "Wartość musi mieć długość od %2 do %3 znaków",
                "invalid zip code" : "Nieprawidłowy kod pocztowy",
                "nrb must contain only digits and spaces" : "Numer Konta Bankowego może zawierać tylko cyfry i spacje",
                "nrb must contain %2 digits" : "Numer Konta Bankowego musi składać się z %2 cyfr",
                "invalid nrb" : "Nieprawidłowy Numer Konta Bankowego",
                "invalid phone number" : "Numer telefonu powinien składać się z dziewięcu cyfr",

                "no_access_token:admin" : "Błąd w komunikacji z Google, kliknij <a href=\"%1\">TUTAJ</a>, aby odświeżyć uprawnienia.",
                "no_access_token:info"  : "Błąd w komunikacji z Google, skontaktuj się z administratorem.",

                "edit"  : "Edytuj",
                "add"   : "Dodaj",
                "calendar" : "Rozplanuj",
                "googleCalendar" : "Dodaj do swojego kalendarz Google",
                "generateReport" : "Raport",
                "close" : "Zamknij",
                "general:name" : "Nazwa",
                "logWithGoogle" : "Zaloguj przez Google Apps",

                //errors
                "io.request:error400" : "Nieprawidłowe żądanie",
                "io.request:error401" : "Nie masz uprawnień do żądanego zasobu",
                "io.request:error404" : "Żądany zasób nie został znaleziony",
                "io.request:error405" : "Akcja niedozwolona",
                "io.request:error406" : "Nieprawidłowe parametry żądania",
                "io.request:error409" : "Wystąpił nieoczekiwany konflikt",
                "io.request:error500" : "Wystąpił nieoczekiwany błąd serwera",

                "error occured" : "Przepraszamy, wystąpił błąd",

                'error.user_exists'             : 'Taka nazwa użytkownika już istnieje',
                "user with the same username or email already exists" : 'Użytkownik o podanej nazwie lub adresie email już istnieje',
                "coach is assigned to some course units" : "Nie można usunąć trenera, ponieważ jest przypisany do co najmniej jednej jednostki tematycznej",
                "training center is assigned to some courses" : "Nie można usunąć ośrodka, ponieważ jest przypisany do co najmniej jednego szkolenia",
                "room is assigned to some lessons" : "Nie można usunąć sali, ponieważ jest przypisana do co najmniej jednej lekcji",
                "coach must have admin or coach role" : "Trener musi byc w roli administratora lub trenera",

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

                "calendar.lesson.collisions:edit_completed"     : "Próbujesz edytować lekcję, która już się odbyła",
                "calendar.lesson.collisions:backward_date"      : "Podano datę wcześniejszą niż dzisiejsza.",
                "calendar.lesson.collisions:end_before_start"   : "Końcowa godzina jest późniejsza niż godzina początkowa",

                "calendar.lesson.collisions:found"       : "Znaleziono następujące kolizje przy dodawaniu lekcji: ",
                "calendar.lesson.collisions:room"        : "zajęcia w tym samym pomieszczeniu",
                "calendar.lesson.collisions:coach"       : "zajęcia z tym samym trenerem",
                "calendar.lesson.collisions:course_unit" : "zajęcia tej samej grupy",

                "calendar.lesson.recurring:is" : "powtórz wydarzenie",

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
                "form.course:show_on_www"          : "Pokaż na WWW",

                "form.course:button cancel"        : "Anuluj",
                "form.course:button add"           : "Dodaj szkolenie",
                "form.course:button edit"          : "Zapisz zmiany",
                "form.course:window add"           : "Dodawanie nowego szkolenia",
                "form.course:window edit"          : "Edytowanie informacji o szkoleniu",
                "form.course:added"                : "Szkolenie zostało dodane!",
                "form.course:edited"               : "Zmiany zostały zapisane",

                'form.passwordReminder:window'          : 'Przypominanie hasła',
                'form.passwordReminder:email'           : 'E-mail',
                'form.passwordReminder:button cancel'   : 'Zamknij',
                'form.passwordReminder:button add'      : 'Wyślij',
                'form.passwordReminder:label'           : 'Na podany adres e-mail zostanie wysłana wiadomość z odnośnikiem, w który należy kliknąć by poznać swoje hasło.',

                "survey.addquestion:title"     : "Treść",
                "survey.addquestion:help"      : "Tekst pomocniczy",
                "survey.addquestion:type"      : "Typ pytania",
                "survey.addquestion:name"      : "Treść",
                "survey.addquestion:required"  : "Wymagane",
                "survey.create:save"           : "Zapisz",
                "survey.create:send"           : "Wyślij",
                "survey.create:cancel"         : "Anuluj",
                "surveys.user.no_results"      : "Wyniki nie są dostępne. Ankieta nie została wypełniona.",
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
                "groupSummary"                 : "Wyniki grupy",
                "detailedResults"              : "Zobacz wyniki",
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
                "surveys.type.created_by"      : "Autor",
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
                "quiz.list.time_limit"         : "Limit czasowy (mm:ss)",
                "quiz.list.start_time"         : "Data rozpoczęcia",
                "quiz.list.score"              : "Punkty",
                "quiz.list.level"              : "Poziom",
                "quiz.list.group_name"         : "Grupa",
                "quiz.list.username"           : "Uczestnik",
                "quiz.list.www"                : "Adres WWW",
                "quiz.list.tab:all"            : "Wszystkie",
                "quiz.list:search"             : "Wyszukaj...",
                "quiz.list:confirm removal"    : "Potwierdź usunięcie",
                "quiz.list:quiz was removed"   : "Quiz został usunięty",
                "quiz.list:add new"            : "Dodaj nowy quiz",
                "sendQuiz"                     : "Wyślij",
                "runQuiz"                      : "Uruchom quiz",
                "quizResults"                  : "Wyniki",

                "_previewImageIcon_:go-previous"    : "Poprzedni",

                "_previewImageIcon_:go-next"        : "Następny",
                "_previewImageIcon_:zoom-in"        : "Powiększ",
                "_previewImageIcon_:edit-delete"    : "Usuń",
                "quiz.addquiz:name"             : "Nazwa quizu",
                "quiz.tab:results"           : 'Wyniki quizu',
                "form.quiz:name"             : "Nazwa quizu",
                "form.quiz:description"      : "Opis",
                "form.quiz:time_limit"       : "Limit czasowy (sekundy)",
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
                "form.training_center.add:code"              : 'Kod ośrodka',
                "form.training_center.add:zip_code"          : 'Kod pocztowy',
                "form.training_center.add:city"              : 'Miasto',
                "form.training_center.add:caption"           : 'Dodaj ośrodek szkoleniowy',
                "form.training_center.add:success"           : 'Dodano ośrodek szkoleniowy',
                "form.training_center.add:manager"           : 'Kierownik',
                "form.training_center.add:url"               : 'Strona internetowa',
                "form.training_center.add:room_amount"       : 'Liczba sal',
                "form.training_center.add:seats_amount"      : 'Liczba miejsc',
                "form.training_center.add:phone_number"      : 'Telefon kontaktowy',
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

                "form.lesson:repeat.how"                    : "Powtarzaj:",
                "form.lesson:repeat.how.howMuch"            : "Co ile tygodni",
                "form.lesson:repeat.in"                     : "Powtarzaj w: ",
                "form.lesson:repeat.finish"                 : "Koniec:",
                "form.lesson:repeat.finish.after.x.times"   : "Powtarzaj tyle razy:",
                "form.lesson:repeat.finish.in.day"          : "Skończ tego dnia:",

                "form.lesson:hourStart"       : "Godzina rozpoczecia",
                "form.lesson:hourEnd"     : "Godzina zakończenia",

                "form.training_center.add:pageGeneral"       : 'Ogólne',
                "form.training_center.add:pageDescription"   : 'Opis ośrodka',
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
                "form.project:is_default"           : 'Domyślny',
                "form.project:success"              : 'Dodano projekt',
                "form.project:start_date"           : 'Start projektu',
                "form.project:end_date"             : 'Koniec projektu',
                "form.project:button add"           : 'Dodaj projekt',
                "form.project:window add"           : 'Dodawanie nowego projektu',
                "form.project:button edit"          : 'Zapisz zmiany',
                "form.project:window edit"          : 'Edytowanie danych o projekcie',
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

                "form.user:window add"                  : 'Dodawanie nowego użytkownika',
                "form.user:window edit"                 : 'Edycja danych o koncie',
                "form.user:username"                    : 'Nazwa użytkownika',
                "form.user:first_name"                  : 'Imię',
                "form.user:last_name"                   : 'Nazwisko',
                "form.user:plain_password"              : 'Hasło',
                "form.user:email"                       : 'E-mail',
                "form.user:role_id"                     : 'Rola',
                "form.user:button cancel"               : 'Anuluj',
                "form.user:button add"                  : 'Dodaj',
                "form.user:button edit"                 : 'Zapisz',
                "form.user.error.invalid_username"      : 'Nazwa użytkownika może zawierać tylko litery, cyfry oraz znak "_"',

                "lib.grid.Abstract.delete-record-confirm"       : 'Na pewno chcesz usunać daną pozycję?',
                "lib.grid.Abstract.delete-records-confirm"      : 'Na pewno chcesz usunać zaznaczone pozycje?',

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

                "form.exam:name"            : "Kategoria oceny",
                "form.exam:type"            : "typ",
                "form.exam:created_date"    : "data",
                "form.exam:button cancel"   : "Anuluj",
                "form.exam:button add"      : "Dodaj sprawdzian",
                "form.exam:button edit"     : "Zapisz zmiany",
                "form.exam:window add"      : "Dodawanie kategorii oceny",
                "form.exam:window edit"     : "Edytowanie kategorii oceny",
                "form.exam:added"           : "Dodano nową kategorię!",
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
                "form.course_schedule:edited"        : "Zmiany zostaly zapisane",

                "form.report_picker:window add"         : 'Generowanie dokumentu',
                "form.report_picker:name"               : 'Typ dokumentu',
                "form.report_picker:report_format"      : 'Format dokumentu',
                "form.report_picker:generating_report"  : 'Proszę czekać...',
                "form.report_picker:button cancel"      : 'Zamknij',
                "form.report_picker:button add"         : 'Wygeneruj',

                "form.report_picker:date_from"          : "Od",
                "form.report_picker:date_to"            : "Do",

                "form.report_picker:window add:1"       : 'Generowanie listy obecności',
                "form.report_picker:window add:12"      : 'Generowanie E-dziennika',

                "reportPicker.template.PresenceList"                            : 'Lista obecności',
                "reportPicker.template.DoorList"                                : 'Lista na drzwi',
                "reportPicker.template.Certificates"                            : 'Zaświadczenia',
                "reportPicker.template.CertificatesReceiveConfirmation"         : 'Potwierdzenie odbioru zaświadczeń',
                "reportPicker.template.LoginsReceiveConfirmation"               : 'Potwierdzenie odbioru loginów',
                "reportPicker.template.TrainingMaterialsReceiveConfirmation"    : 'Potwierdzenie odbioru materiałów',
                "reportPicker.template.CourseSchedule"                          : 'Harmonogram szkolenia',
                "reportPicker.template.RegistrationForm"                        : 'Karta zgłoszeniowa',
                "reportPicker.template.SurveyResults"                           : 'Wyniki ankiety',
                "reportPicker.template.NewStudent"                              : 'Karta zgłoszeniowa',
                "reportPicker.template.PefsForAll"                              : 'Dane do PEFS',
                "reportPicker.template.Ejournal"                                : 'E-dziennik',

                "mailbox:folder1" : "Skrzynka odbiorcza",
                "mailbox:folder2" : "Skrzynka nadawcza",
                "mailbox:folder3" : "Kosz",

                "addressbook.tab:groups"    : "Grupy",
                "addressbook.tab:users"     : "Użytkownicy",
                "addressbook.tab:trainers"  : "Trenerzy",

                "form.compose:recipient_list"   : "Odbiorca",
                "form.compose:subject"          : "Temat",
                "form.compose:attachment"       : "Załącznik",
                "form.compose:window add"       : "Tworzenie nowej wiadomości",

                "user.profile.tab:personal" : "dane osobowe",
                "user.profile.tab:contact" : "dane kontaktowe",
                "user.profile.tab:work" : "miejsce pracy / nauki",
                "user.profile.tab:tax" : "urząd skarbowy",
                "user.profile.tab:zus" : "ZUS",

                "user.profile.personal:first_name" : "imię",
                "user.profile.personal:last_name" : "nazwisko",
                "user.profile.personal:sex" : "płeć",
                "user.profile.personal:national_identity" : "pesel",
                "user.profile.personal:birth_date" : "data urodzenia",
                "user.profile.personal:birth_place" : "miejsce urodzenia",
                "user.profile.personal:education" : "wykształcenie",
                "user.profile.personal:care_children_up_to_seven" : "opieka nad<br/>dziećmi do lat 7",
                "user.profile.personal:care_dependant_person" : "opieka nad<br/>osobą zależną",
                "user.profile.personal:personal_status" : "status osoby",
                "user.profile.personal:teacher_of" : "nauczany przedmiot",

                "user.profile.personal:group_headmaster" : "grupa uczestników",
                "user.profile.personal:group_headmaster.label" : "dyrektor/wicedyrektor",
                "user.profile.personal:group_project_leader" : " ",
                "user.profile.personal:group_project_leader.label" : "lider szkolnego projektu",
                "user.profile.personal:group_guardian" : " ",
                "user.profile.personal:group_guardian.label" : "opiekun zespołu uczniowskiego",
                "user.profile.personal:group_student" : " ",
                "user.profile.personal:group_student.label" : "uczeń",
                "user.profile.personal:group_education_staff" : " ",
                "user.profile.personal:group_education_staff.label" : "kadra światowa JST",

                "user.profile.contact:poland_id" : "województwo<br/><br/>powiat<br/><br/>gmina",
                "user.profile.contact:address_city" : "miasto",
                "user.profile.contact:address_zip_code" : "kod pocztowy",
                "user.profile.contact:address_street" : "ulica",
                "user.profile.contact:address_house_nr" : "nr domu",
                "user.profile.contact:address_flat_nr" : "nr mieszkania",
                "user.profile.contact:phone_number" : "tel. stacjonarny",
                "user.profile.contact:fax_number" : "fax",
                "user.profile.contact:mobile_number" : "tel. komórkowy",
                "user.profile.contact:region" : "obszar",
                "user.profile.contact:administration_region" : "obszar administracyjny",

                "user.profile.work:work_name" : "firma",
                "user.profile.work:work_city" : "miasto",
                "user.profile.work:work_zip_code" : "kod pocztowy",
                "user.profile.work:work_street" : "adres",
                "user.profile.work:work_tax_identification_number" : "NIP firmy",
                "user.profile.work:work_poland_id" : "województwo<br/><br/>powiat<br/><br/>gmina",

                "user.profile.tax:tax_identification_number" : "Twój NIP",
                "user.profile.tax:tax_office" : "urząd skarbowy",
                "user.profile.tax:tax_office_address" : "ulica",
                "user.profile.tax:identification_name" : "rodzaj dokumentu",
                "user.profile.tax:identification_name_example" : "np. Dowód osobisty",
                "user.profile.tax:identification_number" : "seria i numer dowodu",
                "user.profile.tax:identification_number_example" : "np. ABC12345",
                "user.profile.tax:identification_publisher" : "wydawca dowodu",
                "user.profile.tax:father_name" : "imię ojca",
                "user.profile.tax:mother_name" : "imię matki",
                "user.profile.tax:nfz" : "NFZ",
                "user.profile.tax:bank" : "Numer Konta Bankowego",

                "user.profile.tax:tax_office_poland_id" : "województwo<br/><br/>powiat<br/><br/>gmina",
                "user.profile.tax:tax_office_city" : "miasto",
                "user.profile.tax:tax_office_zip_code" : "kod pocztowy",
                "user.profile.tax:tax_office_house_nr" : "nr domu",
                "user.profile.tax:tax_office_country" : "kraj",
                "user.profile.tax:tax_office_post_city" : "poczta",

                "user.profile.zus:zus" : "ZUS",

                "user.profile:button-submit" : "Zapisz",
                "user.profile:button-cancel" : "Anuluj",
                "user.profile:button-print"  : "Drukuj kartę zgłoszeniową",

                "user.account:username" : "nazwa użytkownika",
                "user.account:email" : "email",
                "user.account:new_password" : "nowe hasło",
                "user.account:retype_password" : "powtórz hasło",
                "user.account:button cancel" : "Anuluj",
                "user.account:button add" : "Zapisz zmiany",

                'form.passwordReminder:added' : 'Jeśli podany e-mail był prawidłowy, wysłano na niego dalsze instrukcje',

                "ejournal.info.label.group"   : "Grupa",
                "ejournal.info.label.course"  : "Szkolenie",
                "ejournal.info.label.unit"    : "Jednostka",
                "ejournal.info.label.trainer" : "Trener",

                "table.row.button:edit" : "Edytuj",
                "table.row.button:delete" : "Usuń",
                "table.row.button:profile" : "Profil",
                "table.row.button:usercourseinfo" : "Informacje o szkoleniach",
                "table.row.button:syncgroup" : "Synchronizuj grupę",

                "import_users.button:label"   : "Importuj",
                "import_users.button:tooltip" : "Importuj użytkowników z google do aplikacji",
                "import_users.window:caption" : "Importowanie użytkowników z google do aplikacji",

                "import_groups.button:label"   : "Importuj",
                "import_groups.button:tooltip" : "Importuj grupy z google do aplikacji",
                "import_groups.window:caption" : "Importowanie użytkowników z google do aplikacji",

                "group.manager.error:invalid_google_group_id" : "Dozwolone znaki: nie-polskie litery, liczby, podkreślnik, myślnik, kropka",
                "group.manager:google_group_id" : "identyfikator grupy",
                "group.manager:button cancel"   : "Anuluj",
                "group.manager:button edit"     : "Zapisz",

                "group.sync.mode:import" : "google > yala",
                "group.sync.mode:export" : "yala > google",
                "group.sync.mode:merge"  : "połącz",

                "group.sync.mode.tooltip:import" : "Importuj użytkowników grupy",
                "group.sync.mode.tooltip:export" : "Eksportuj użytkowników grupy",
                "group.sync.mode.tooltip:merge"  : "Połącz użytkowników grupy",

                "group.sync:window caption"  : "Wybierz metodę synchronizacji",
                "group.sync:success"  : "Grupa została zsynchronizowana",
                "group.sync:no group selected"  : "Nie wybrano grupy",

                "form.project.tab:general" : "Informacje o projekcie",
                "form.project.tab:leaders" : "Kierownik projektu",
                "form.project.tab:extra_fields" : "Dodatkowe pola",
                "form.project:extra_fields_intro" : "Tutaj możesz definiować dodatkowe pola do formularza rejestracji na szkolenie przez moduł WWW. Kliknij tutaj, aby zobaczyć więcej informacji.",
                "form.project:extra_fields_info" : " Format:" +
                    "<br/>" +
                    "<span style='font-weight:bold;'>nazwa_pola</span>:<span style='color:red;'>pole_wymagane</span>:<span style='color:blue;'>opcje</span>" +
                    "<br/>" +
                    "gdzie:" +
                    "<br/>" +
                    "<ul>" +
                        "<li><span style='color:red;'>pole_wymagane</span>: 0 lub 1</li>" +
                        "<li><span style='color:blue;'>opcje</span>:" +
                            "<ul>" +
                                "<li>brak - pole tekstowe</li>" +
                                "<li>wartości oddzielone przecinkiem - pole wybory typu \"select\"</li>" +
                                "<li>wartości oddzielone znakiem \"#\" - pole wieloktornego wyboru typu \"checkbox\"</li>" +
                                "<li>wartości oddzielone znakiem \"|\" - pole jednokrotnego wyboru typu \"radio\"</li>" +
                            "</ul>" +
                        "</li>" +
                    "</ul>" +
                    "<br/>" +
                    "Przykłady:" +
                    "<br/>" +
                    "<ul>" +
                        "<li>Imię ojca:0</li>" +
                        "<li>Liczba dzieci:1:brak,1,2,więcej</li>" +
                        "<li>Zainteresowania:0:turystyka#technologia#sport</li>" +
                        "<li>Kolor oczu:1:zielony|niebieski|brązowy</li>" +
                    "</ul>"
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
                    day_short:      ["Nd", "Pon", "Wt", "Śr", "Czw", "Pt", "Sb"]
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
                    date_s: "Od:",
                    date_e: "Do:"
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
        },

        objIndexOf : function(array, propertiesName, propertiesValue)
        {
            for(var i = 0, length = array.length; i < length; i++)
            {
                if(array[i][propertiesName] === propertiesValue)
                {
                    return i;
                }
            }

            return -1;
        },

        mergeObjects : function(objectTo, objectFrom) {
                for (var attribute in objectFrom) {
                    objectTo[attribute] = objectFrom[attribute];
                }
        }
    }
});