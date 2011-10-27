var layer_open = false;

function showLayer() {
	//$('.calendarEventsLayer').setProperty('display', 'block');	// id
	$$('.calendarEventsLayer')[0].setProperty('display', 'block');	// class
	updateTooltips();
}
function closeLayer(el) {
	layer_open = false;
	el.getParents('.calendarEventsLayer')[0].dispose();
	$$('.day', '.day_sel').getParent('div').setStyle('position', 'static');
}
function onDayClickAction(wideView, date, clickel, instance) {

	if(wideView==1) {
		var ctg = $('selctg').value;
		$$('#cal_calendar_'+instance+' .calendarBody')[0].getElements('div[class$=sel]').each(function(el) {
				 el.setProperty('class', el.getProperty('class').replace('_sel', ''));
				});
		clickel.setProperty('class', clickel.getProperty('class')+'_sel');
		ajaxRequest('post', 'index.php?pt['+instance+'-renderLeft]', 'date='+date+'&ctg='+ctg, $$('#cal_calendar_'+instance+' td[class=calendarLeft]')[0], {'script':true, 'cache':true, 'callback':updateTooltips});
	}
	else {
		if(layer_open) return false;
		$$('#cal_calendar_'+instance+' .calendarBody')[0].getElements('div[class^=day]').each(function(el) {el.setStyle('position', 'static')});
		clickel.getNext('div').setStyle('position', 'relative');
		$$('#cal_calendar_'+instance+' .calendarBody')[0].getElements('div[class$=sel]').each(function(el) {
				 el.setProperty('class', el.getProperty('class').replace('_sel', ''));
				});
		var clickelclass = clickel.getProperty('class');
		clickel.setProperty('class', clickel.getProperty('class')+'_sel');
		layer_open = true;
		var layer = new Element('div', {
				'class':'calendarEventsLayer' 
		});
		layer.set("html", "<div style=\"float:left; padding-left:2px;\">eventi:</div><div id=\"calendarLayerClose\" onclick=\"closeLayer($(this))\"></div><div class=\"null\"></div>");
		layer.inject(clickel.getNext(), 'top');
		var layerEventsField = new Element('div', {'class':'calendarEventsField'});
		layerEventsField.inject(layer, 'bottom');
		ajaxRequest('post', 'index.php?pt['+instance+'-getDateEvents]', 'date='+date, layerEventsField, {'script':true, 'cache':true, 'callback':showLayer});
	}
}
