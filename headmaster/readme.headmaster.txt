1. Technologia
Gra HEADMASTER powstała w oparciu o popularny Zend Framework 1.1.11 z wykorzystaniem biblioteki JavaScript jQuery w wersji 1.7 + jQuery UI w wersji 1.8.1 oraz system migracji bazy danych Doctrine.

2. Wymagania systemowe
Minimalne:
- serwer WWW: Apache2 z włączonym modułem rewrite
- PHP 5.1
- serwer bazy danych: PostgreSQL 8.4

Zalecane:
- serwer WWW: Apache2 z włączonym modułem rewrite
- PHP 5.3
- serwer bazy danych: PostgreSQL 9.0
- Narzędzie Phing
- Narzędzie Cron

3. Instalacja
a) Uruchomić skrypt instalujący poleceniem:
sh headmaster.sh
b) Postępować wg instrukcji wyświetlanych przez instalator
c) W ustawieniach VirtualHost’a serwera WWW, ustawić DocumentRoot na katalog public

4. Pierwsze logowanie
Pierwszy użytkownik, który zaloguje się do aplikacji będzie administratorem gry.

5. Zawartość
5.1. Struktura katalogów
Struktura katalogów podzielona jest w następujący sposób:
- application - główny katalog aplikacji
- library - katalog zawierający biblioteki wykorzystywane w aplikacji
- public - katalog, którego głównym elementem jest plik index.php, będący wejściem do aplikacji przez użytkownika - przez niego przechodzą wszystkie akcje
- scripts - zawiera pliki i skrypty potrzebne do obsługi gry.

5.2. Application
W katalogu application znajduje się kod gry. Struktura tego katalogu jest zgodna z założeniami wzorca MVC (and. Model-View-Controller), który zakłada podział aplikacji na trzy główne części:
- model - jest reprezentacją problemu lub logiki aplikacji (katalog models)
- widok - opisuje, jak wyświetlić pewną część modelu w ramach interfejsu użytkownika. Może składać się z podwidoków odpowiedzialnych za mniejsze części interfejsu (katalog views)
- kontroler -  przyjmuje dane wejściowe od użytkownika i reaguje na jego poczynania, zarządzając aktualizacje modelu oraz odświeżenie widoków (katalog controllers)
Ponadto znajdują się także katalogi:
- cache zawierający pliki tymczasowe, służące do zapisywania danych, które rzadko się zmieniają
- forms i grids, w których znajdują się, odpowiednio, definicje formularzy i tabel z danymi, wykorzystywanych w grze
- resources, który zawiera pliki językowe i tłumaczenia.
Plik Bootstrap.php zawiera logikę pozwalającą na załadowanie wszystkich potrzebnych zasobów aplikacji, w tym mechanizm ACL (ang. Access Control List). Odpowiada także za zainicjowanie połączenia z bazą danych oraz wczytanie danych o aktualnie zalogowanym użytkowniku.

5.3. Library
Katalog library zawiera gotowe biblioteki zewnętrzne, wykorzystywane w aplikacji, z których podstawową jest biblioteka Zend. Oprócz tego wyróżnić można biblioteki:
- Doctrine, która zawiera mechanizm do migracji bazy danych do najnowsze wersji
- Bvb, służącą do jednolitego przedstawiania danych tabelarycznych
- ZendX, pozwaląca na integrację JavaScript’owej biblioteki jQuery z Zend’em
- sfYaml, upraszczająca zapisywanie i odczytywanie danych konfiguracyjnych plików w formacie yaml
- Game oraz GN, zawierające różne mechanizmy i funkcjonalności wykorzystywane w innych projektach
- Playgine, będącą silnikiem gry IPK odpowiedzialnym m.in. za odpalanie kolejki.

5.4. Public
Zawiera pliki graficzne oraz arkusze stylów wykorzystywane w aplikacji.

5.5. Scripts
Przydatne pliki skryptowe, służące do obsługi aplikacji, np. aktualizacji bazy danych do najnowszej wersji.
