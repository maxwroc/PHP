

var _ge = function(id) { return document.getElementById(id); };

if (xajax) {
	xajax.config.status.update = function() {
		var icon = _ge('loadingIcon');
		var rasp_icon = _ge('raspico');
		return {
			onRequest: function() {
				//icon && (icon.style.display = 'block');
				rasp_icon && (rasp_icon.className = 'loading');
			},
			onWaiting: function() {
			},
			onProcessing: function() {
			},
			onComplete: function() {
				//icon && (icon.style.display = 'none');
				rasp_icon && (rasp_icon.className = '');
			}
		}
	};
}

var ServiceManager = new function() {

	this._services = {};
	
	this.init = function(tableId) {
		
		var table = _ge(tableId);
		if (!table) {
			alert('Brak elementu ' + tableId);
			return;
		}
		
		var services = table.getElementsByTagName('TR');
		for (var i = 0, service; service = services[i];){
			var metadata = service.getAttribute('metadata');
			if (metadata) {
				var data = JSON.parse(metadata);
				
				var anchors = service.getElementsByTagName('A');
				for (var j = 0, link; link = anchors[j];) {
					anchor.href = "javascript:void(0);";
				}
			}
		}
	};
	
	this.click = function(anchor) {
		var metadata = anchor.getAttribute('metadata');
		if (metadata) {
			try {
				metadata = metadata.replace(/'/g, '"');
				var data = JSON.parse(metadata);
				
				data.img = _ge( data.i );
				if (!data.img) {
					alert("JS: image [" + data.i + "] for [" + data.s + "] service not found.");
					return;
				}
		
				data.img.className = data.img.className.replace(/\s?(in)?active/,'');
				
				this._services[data.s] = data;
				
				xajax_serviceChangeAjax( data.s, data.a );
			}
			catch(e) {
				alert("Error occurred:\n" + e + "\n\n" + metadata);
			}
		}
	}
	
	this.refresh = function() {
		// refresh status after clicking on the icon
	}
	
	this.update = function(service, isActive) {
		var service = this._services[service];
		
		if (!service) {
			alert("JS: service not found");
			return;
		}
		
		service.img.className += " " + (isActive ? "" : "in") + "active";
	}
}