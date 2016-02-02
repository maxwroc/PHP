<div class="box">
  <p>To perform the full scan of preselected directories please press the below &quot;Scan&quot; button.</p>
  <p>This action will also remove obsolete DB entries for files that are no longer on the disk.</p> 
  <br />
  <p>To load files from DB which don't have a movie assigned click on &quot;Load&quot;</p>
  <div class="btn" onclick="xajax_scanAjax()">Scan for new files</div> <div class="btn" onclick="xajax_loadFilesWithoutMoviesAjax()">Load files without movies</div>
</div>
<div class="cf splitbox" data-bind="visible: (newFiles().length > 0 || deletedFiles().length > 0) " style="display:none">
  <div class="head">
    <h3>Results</h3>
  </div>
  <div class="sidebyside">
    <div class="head">
      <h2>New files (<span data-bind="text: newFiles().length"></span>)</h2>
    </div>
    <table class="smalltable" data-bind="visible: newFiles().length > 0">
      <thead>
        <tr>
          <th>File name <span data-bind="text: newFilesPage"></span> <span class="text-right"><a href="javascript:pageViewModel.movePage(-1)">&lt;&lt;</a> <a href="javascript:pageViewModel.movePage(1)">&gt;&gt;</a></span></th>
        </tr>
      </thead>
      <tbody data-bind="template: { name: 'table-row', foreach: newFiles }"></tbody>
    </table>
  </div>
  <div class="sidebyside">
    <div class="head">
      <h2>Deleted files (<span data-bind="text: deletedFiles().length"></span>)</h2>
    </div>
    
    <table class="smalltable" data-bind="visible: deletedFiles().length > 0">
      <thead>
        <tr>
          <th>File name</th>
        </tr>
      </thead>
      <tbody data-bind="template: { name: 'table-row', foreach: deletedFiles }"></tbody>
    </table>
  </div>
</div>

<?php echo $mMovieInfo; ?>

<script type="text/html" id="table-row">
  <tr data-bind="attr: { class: $data.file_id ? 'clickable ' : '', onclick: $data.file_id ? 'pageViewModel.getDetails(' + $data.file_id + ', ' + $index() + ')' : '' }">
    <td data-bind="text: (typeof $data == 'string' ? $data : $data.path)"></td>
  </tr>
</script>
