<?php
$aLang = array(

E_ERROR			=> array( 'PHP Fatal Error', '<strong>Error:</strong> %s<br /><strong>File:</strong> %s<br /><strong>Line:</strong> %d' ),
E_WARNING		=> array( 'PHP Warning', '<strong>Error:</strong> %s<br /><strong>File:</strong> %s<br /><strong>Line:</strong> %d' ),
E_PARSE			=> array( 'PHP Parse Error', '<strong>Error:</strong> %s<br /><strong>File:</strong> %s<br /><strong>Line:</strong> %d' ),
E_NOTICE		=> array( 'PHP Notice', '<strong>Error:</strong> %s<br /><strong>File:</strong> %s<br /><strong>Line:</strong> %d' ),
E_USER_ERROR	=> array( 'PHP User Error', '<strong>Error:</strong> %s<br /><strong>File:</strong> %s<br /><strong>Line:</strong> %d' ),
E_USER_WARNING	=> array( 'PHP User Warning', '<strong>Error:</strong> %s<br /><strong>File:</strong> %s<br /><strong>Line:</strong> %d' ),
E_USER_NOTICE	=> array( 'PHP User Notice', '<strong>Error:</strong> %s<br /><strong>File:</strong> %s<br /><strong>Line:</strong> %d' ),
E_STRICT		=> array( 'PHP Strict Error', '<strong>Error:</strong> %s<br /><strong>File:</strong> %s<br /><strong>Line:</strong> %d' ),
E_RECOVERABLE_ERROR	=> array( 'PHP Recoverable Error', '<strong>Error:</strong> %s<br /><strong>File:</strong> %s<br /><strong>Line:</strong> %d' ),

'controller_not_found' 	=> array( 'Nie odnaleziono kontrolera.', '<strong>Nazwa:</strong> %s<br /><strong>Aplikacja:</strong> %s' ),
'action_not_found' 		=> array( 'Nie odnaleziono wybranej akcji.', '<strong>Kontroler:</strong> %s<br/><strong>Akcja:</strong> %s<br/><strong>Parametry:</strong> %s' ),
'module_exception' 		=> array( 'Błąd modułu.', '%s' ),
'model_vars_not_set' 	=> array( 'Błąd w klasie modelu danych.', 'Brak definicji sTable lub sPrimaryKey w klasie %s' ),
'model_assigned_incorrect_model_object' => array( 'Błąd w klasie modelu danych.', 'Brak wpisu <strong>%s</strong> w aHasMany modelu <strong>%s</strong>.' ),
'database_factory_not_set' => array( 'Blad w klasie modelu dnaych', 'Brak obiektu Database w %s' ),
'object_property_not_set' => array( 'Blad inicjalizacji klasy', 'Brak ustawionego pola <strong>%s</strong> w klasie' ),
'incorrect_func_arg' 	=> array( 'Blad wywoania funkcji', 'Argument uzyty do wywolania metody jest niepoprawny' ),
'conig_value_not_found' => array( 'Blad odczytu konfiguracji', 'Wartosc dla klucza <strong>%s</strong> nie odnaleziona' ),
'controller_template_name_not_set' => array( 'Błąd kontrolera.', 'Brak ustawionej wartości mTemplate w kontrolerze <strong>%s</strong>' ),
'controller_module_without_constructor' => array( 'Błąd kontrolera.', 'Brak możliwości wywołania konstruktora z podanymi parametrami. Moduł <strong>%s</strong> nie posiada zdefiniowanego konstruktora' ),
);
