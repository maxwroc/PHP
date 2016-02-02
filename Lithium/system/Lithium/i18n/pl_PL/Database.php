<?php
$aLang = array(
'error_config' => array( 'Nie odnaleziono konfiguracji bazy.', 'Sprawdz plik konfiguracyjny aplikacji.' ),
'error_wrong_config_keys' => array( 'Błędne nazwy zmiennych w pliku koniguracyjnym dot. danych bazy.', 'Zmienne jakie powinny się tam znajdować to: type, host, user, pass, database, persistent.' ),
'error_driver_not_found' => array( 'Nie odnaleziono sterownika bazy.', 'Sprawdź poprawność wpisu &quot;type&quot; w pliku konfiguracyjnym w sekcji &quot;Database&quot;.' ),
'connection' => array( 'Problem z polaczeniem z baza.', '%s %s' ),
'query_failure' => array( 'Problem z wykonaniem zapytania.', '<div class="code_block">%s</div><br /><strong>%s</strong>' ),
'incorrect_query_params' => array( 'Błąd zapytania.', 'Niepoprawna liczba argumentow dla podanego zapytania.' ),
'incorrect_query_type' => array( 'Niepoprawny typ zapytania', 'Zapoznaj się z dokumentacją. Typy zapytań zdefiniowane są w klasie Database_Query.' ),
'incorrect_condition_type' => array( 'Błąd zapytania.', 'Niepoprawny typ warunku: "%s". Typy warunków możliwych do wykorzystania w zapytaniu zdefiniowane są w klasie Database_Query.' ),
'columns_array_not_set' => array( 'Błąd modelu danych.', 'Brak zdefiniowanych kolumn w modelu bazy danych.' ),
'matching_variables_failed' => array( 'Błąd zapytania.', 'Błąd podczas parsowania parametrów zapytania <strong>%s</strong>.' ),
'model_incorrect_column_name' => array( 'Błąd modelu danych.', 'Model <strong>%s</strong> nie posiada kolumny <strong>%s</strong>' ),
'model_unknown_column_type' => array( 'Błąd modelu danych.', 'Nieznany typ kolumny <strong>%s</strong>.' ),
'model_insert_columns_inconsistency' => array( 'Błąd modelu danych.', 'Podczas operacji grupowego dodawania rekordów ich kolumny powinny być jednakowe.' ),
'insert_columns_not_set' => array( 'Błąd zapytania sql.', 'Brak nazw kolumn przekazanych do zapytania typu INSERT.' ),
'update_fields_not_set' => array( 'Błąd zapytania sql.', 'Brak danych w zapytaniu typu UPDATE.' ),
'missing_column_info' => array( 'Błąd zapytania sql.', 'Brak definicji dla kolumny <strong>%s</strong>.' ),
);
