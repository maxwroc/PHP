<div class="cf splitbox" data-bind="visible: movieDetails, with: movieDetails">
  <div class="head">
    <h3>Movie info</h3>
    <div class="text-right" data-bind="visible: $root.showNavigation">
      <a href="javascript:pageViewModel.moveToNextInfo(-1)">&lt;&lt;</a>
      <a href="javascript:pageViewModel.moveToNextInfo(1)">&gt;&gt;</a>
    </div>
  </div>
  <div class="sidebyside">
    <div class="head">
      <h2>Details</h2>
    </div>
    <form id="movie-form">
      <input type="hidden" name="file_id" value="" data-bind="attr: { value: $data.file_id }" />
      <input type="hidden" name="movie_id" value="" data-bind="attr: { value: $data.movie_id }" />
      <div class="centerAligned">
        <p><label for="file_path">File path</label> <input name="file_path" id="file_path" type="text" class="longfield" data-bind="attr: { value: $data.path }" /></p>
        <p><label for="name">Original name</label> <input name="name" id="name" type="text" data-bind="attr: { value: $data.name }" /></p>
        <p><label for="local_name">Local name</label> <input name="local_name" id="local_name" type="text" data-bind="attr: { value: $data.local_name }" /></p>
        <p><label for="year">Year</label> <input name="year" id="year" type="text" data-bind="attr: { value: $data.year }" /></p>
        <p><label for="actors">Actors</label> <input name="actors" id="actors" type="text" data-bind="attr: { value: $data.actors }" /></p>
        <p><label for="img_url">Image URL</label> <input name="img_url" id="img_url" type="text" data-bind="attr: { value: $data.img_url }" /></p>
      </div>
      <p><input class="btn" type="button" value="Download movie info" onclick="pageViewModel.getMovieInfo()"/><input class="btn" type="button" value="Save" onclick="pageViewModel.save('movie-form')" /></p>
    </form>
  </div>
  <div class="sidebyside">
    <div class="head">
      <h2>Image</h2>
    </div>
    <div class="movie-image" data-bind="if: $data.img_url"><img src="" data-bind="attr: { src: $data.img_url }" /></div>
    <div data-bind="if: $data.message">
      <div data-bind="text: $data.error, attr: { class: $data.messageType }"></div>
    </div>
  </div>
</div>

<script type="text/javascript">
  var pageViewModel = {
    showNavigation: ko.observable(false),
    
    newFiles: ko.observableArray(null),
    deletedFiles: ko.observableArray(null),
    movieDetails: ko.observable(null),
    movieIndex: null,
    
    newFilesPage: ko.observable(null),
    
    listMaxItemCount: 10,
    
    newFilesList: [],
    newFilesListIndex: 0,
    
    movePage: function(direction) {
      var nextPageIndex = this.newFilesListIndex + (direction * this.listMaxItemCount);
      var nextPageItems = this.newFilesList.slice(nextPageIndex, nextPageIndex + this.listMaxItemCount);
      
      if(nextPageItems.length > 0) {
        this.newFilesListIndex = nextPageIndex;
        this.newFiles(nextPageItems);
        this.updatePageInfo();
      }
    },
    
    updatePageInfo: function() {
      
      var currentPage = (this.newFilesListIndex ? this.newFilesListIndex / this.listMaxItemCount : 0) + 1;
      var pageCount = this.newFilesList.length ? this.newFilesList.length / this.listMaxItemCount : 0;
    
      this.newFilesPage("(" + currentPage + "/" + Math.ceil(pageCount) + ") - " + this.newFilesList.length);
    },
    
    moveToNextInfo: function(direction) {
      var newIndex = this.movieIndex + direction;
      if(newIndex < 0 || !this.newFilesList[newIndex]) {
        return;
      }
      
      this.getDetails(this.newFilesList[newIndex].id, newIndex);
    },
    
    selectItem: function() {
      
    },
    
    getDetails: function(iFileId, iIndex) {
      this.movieIndex = iIndex + this.newFilesListIndex;
      this.selectItem();
      xajax_getFileDetailsAjax(iFileId);
    },
    
    getMovieInfo: function() {
      var oMovie = this.movieDetails();
      xajax_getMovieInfoAjax(oMovie.file_id, oMovie.name ? oMovie.name : oMovie.path);
    },
    
    update: function(oResult) {  
      this.movieIndex = null;
      this.newFilesListIndex = 0;
      this.newFilesList = oResult.newFiles;
      
      // remove movie info pane
      this.movieDetails(null);
    
      this.newFiles(oResult.newFiles.slice(0, this.listMaxItemCount));
      this.updatePageInfo();
      this.showNavigation(this.newFilesList.length > 0);
      
      this.deletedFiles(oResult.deletedFiles);
    },
    
    save: function(sFormId) {
      xajax_saveAjax(xajax.getFormValues(sFormId));
    }
  };

  ko.applyBindings(pageViewModel);

</script>

<?php 
if ( !empty( $aScripts ) ) { 
  echo '<script type="text/javascript">';
  foreach ( $aScripts as $sScript ) {
    echo $sScript;
  }
  echo '</script>';
}
?>
