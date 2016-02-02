<?php

define( 'SYSPATH', '../system/' );
define( 'IN_PRODUCTION', false );

// should be the same as in .htaccess RewriteBase
define( 'URLROOT', '/' );

// default application
$aConfig['default_application'] = 'raspberry';
// configuration for apps
$aConfig['applications']['chodorowski_co']['domain'] = '';
$aConfig['applications']['chodorowski_co']['url_path'] = '';
$aConfig['applications']['chodorowski_co']['config_file'] = 'application/chodorowski.co/config.ini';
$aConfig['applications']['chodorowski_co']['router']['rules'][] = array( '!/groby/(.*?)!', '/donate/\1' );

$aConfig['applications']['blog']['domain'] = 'blog.chodorowski.co';
$aConfig['applications']['blog']['url_path'] = 'blog';
$aConfig['applications']['blog']['config_file'] = 'application/blog/config.ini';
$aConfig['applications']['blog']['router']['rules'][] = array( '!/entry/([0-9]+)/?!', '/blog/show/\1' );

$aConfig['applications']['tagger']['domain'] = 'tagger.chodorowski.co';
$aConfig['applications']['tagger']['url_path'] = '';
$aConfig['applications']['tagger']['config_file'] = 'application/tagger/config.ini';

$aConfig['applications']['netnote']['domain'] = 'note.chodorowski.co';
$aConfig['applications']['netnote']['url_path'] = '';
$aConfig['applications']['netnote']['config_file'] = 'application/netnote/config.ini';

$aConfig['applications']['kalkulator_gpw']['domain'] = 'gpw.chodorowski.co';
$aConfig['applications']['kalkulator_gpw']['url_path'] = '';
$aConfig['applications']['kalkulator_gpw']['config_file'] = 'application/kalkulator_gpw/config.ini';

$aConfig['applications']['squash']['domain'] = 'squash.chodorowski.co';
$aConfig['applications']['squash']['url_path'] = '';
$aConfig['applications']['squash']['config_file'] = 'application/squash/config.ini';

$aConfig['applications']['family']['domain'] = '';
$aConfig['applications']['family']['url_path'] = 'family';
$aConfig['applications']['family']['config_file'] = 'application/family/config.ini';

$aConfig['applications']['mediavault']['domain'] = '';
$aConfig['applications']['mediavault']['url_path'] = 'mediavault';
$aConfig['applications']['mediavault']['config_file'] = 'application/mediavault/config.ini';

$aConfig['applications']['raspberry']['domain'] = '';
$aConfig['applications']['raspberry']['url_path'] = '';
$aConfig['applications']['raspberry']['config_file'] = 'application/raspberry/config.ini';

include( SYSPATH . 'bootstrap.php' );
