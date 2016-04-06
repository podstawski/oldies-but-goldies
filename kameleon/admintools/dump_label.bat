
SET PGPATH=C:\PROGRA~1\PostgreSQL\8.2\bin

SET PGPASSWORD=12345
SET PGUSER=kameleon

SET ver=4.45


echo COPY label TO stdout DELIMITERS ';' ; | %PGPATH%\psql -d kameleondb > ..\changes\label.txt


rem %PGPATH%\pg_dump -s -x -O -d kameleondb >..\changes\postgres-schema-%ver%.sql