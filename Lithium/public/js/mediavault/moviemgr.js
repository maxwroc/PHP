var MovieMgr = new function() {
  var self = this;
  
  self.scanResult = function(oResult) {
    alert("New: \n" + oResult.new.join("\n"));
    alert("Removed: \n" + oResult.removed.join("\n"));
  }
}