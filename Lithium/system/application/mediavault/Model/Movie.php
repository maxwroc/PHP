<?php  

class Model_Movie extends Database_Model {
  
  protected $sTable = 'movies';
  protected $sPrimaryKey = 'movie_id';
  
  protected $aTableInfo = array(
    'movie_id' => 'int',
    'name' => 'varchar',
    'local_name' => 'varchar',
    'year' => 'int',
    'actors' => 'varchar',
    'image_name' => 'varchar'
  ); 
  
  public function getLastError() {
    return $this->sError;
  }
}