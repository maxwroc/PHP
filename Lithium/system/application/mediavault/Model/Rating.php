<?php  

class Model_Rating extends Database_Model {
  
  protected $sTable = 'ratings';
  protected $sPrimaryKey = 'rating_id';
  
  protected $aTableInfo = array(
    'rating_id' => 'int',
    'rating_type_id' => 'int',
    'value' => 'int',
    'movie_id' => 'int'
  );  

  protected $aHasOne = array(
    'movie_id' => 'Movie'
  );
  
  public function getLastError() {
    return $this->sError;
  }
}