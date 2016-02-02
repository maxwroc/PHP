<?php  

class Model_File extends Database_Model {
  
  protected $sTable = 'files';
  protected $sPrimaryKey = 'file_id';
  
  protected $aTableInfo = array(
    'file_id' => 'int',
    'path' => 'varchar',
    'has_subtitles' => 'int',
    'hash' => 'varchar',
    'movie_id' => 'int'
  );  

  protected $aHasOne = array(
    'movie_id' => 'Movie'
  );
  
  public function getLastError() {
    return $this->sError;
  }
}
