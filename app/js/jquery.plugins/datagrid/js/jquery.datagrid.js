// JavaScript Document
(function($) {
$.fn.datagrid = function(options) 
{
		
		var opt = {
			height: 500
		}
		
		
		
		container = this.find('.datagrid-container');
		container.height(opt.height);
	
		input = this.find('.datagrid-input-field');
		btn = this.find('.datagrid-button-search');
		
		btn.click(function () {
			filterTable(input.val());
			input.focus();
			}
		);
				
		table = this.find('table');
		table.addClass('tablesorter');
		
		tbody = table.find('tbody');
		tbody.children('tr').children('td').click(function () {
		   selectCell($(this));
		});
		
		tbody.children('tr').mouseover(function () {
		   $(this).addClass('datagrid-hover-row');
		});
		
		tbody.children('tr').mouseout(function () {
		   $(this).removeClass('datagrid-hover-row');
		});		
		
		thead = table.find('thead');		
		
		selectedRow = tbody.find('tr:first-child');
		selectedCell = selectedRow.find('td:first-child');
		selectCell(selectedCell);
		
		function selectCell(cell) {
			$('.datagrid-selected-cell').removeClass('datagrid-selected-cell');
			cell.addClass('datagrid-selected-cell');
			selectedCell = cell;
			selectedRow = cell.parent();
			selectRow(selectedRow);				
			input.focus();
		}
		
		function selectRow(row) {
			$('.datagrid-selected-row').removeClass('datagrid-selected-row');
			row.addClass('datagrid-selected-row');
		}
		
		function filterTable(text) {
			text = regexEscape(text);
			var filterPatt = new RegExp(text.toUpperCase());
			tbody.find('tr').hide();
			tbody.find('tr').each(
				function() {
					var sVal = $(this).text().toUpperCase().replace(/(\n)|(\r)/ig,'').replace(/\s\s/ig,' ').replace(/^\s/ig,'');
					
					if (filterPatt.test(sVal) === true) {	
							$(this).show();
							//return false;
						}
				}
			)	
		}
		
		function regexEscape(txt, omit) {
				var specials = [
					'/', '.', '*', '+', '?', '|',
					'(', ')', '[', ']', '{', '}', '\\'
					];
				
				if (omit) {
					for (i=0; i < specials.length; i++) {
						if (specials[i] == omit) { specials.splice(i,1); }
					}
				}
				
				var escapePatt = new RegExp('(\\' + specials.join('|\\') + ')', 'g');
				return txt.replace(escapePatt, '\\$1');
			}
		
		if ($.browser.mozilla)
			input.keypress(processKey);	
		else
			input.keydown(processKey);	
			
		function processKey(e) {
			if(e.keyCode == 39 || e.keyCode == 37) e.preventDefault();
			switch(e.keyCode) {
				case 13: {
					selectedRow.dblclick();
				}break;
				
				//left 
				case 37: {
					if(!selectedCell.prev().length)	
						selectCell(selectedRow.find('td:last-child'));	
					else
						selectCell(selectedCell.prev());
				}break;
				
				//right
				case 39: {		
					if(!selectedCell.next().length)	
						selectCell(selectedRow.find('td:first-child'));
					else
						selectCell(selectedCell.next());
					
				}break;
				
				//up
				case 38: {
  					if(!selectedRow.prev().length)
						selectedRow = tbody.find('tr:last-child');
					else
						selectedRow = selectedRow.prev();
					index = selectedCell.attr("cellIndex");
					cell = selectedRow.find('td:eq('+index+')');
					selectCell(cell);					
				}break;
				
				//down
				case 40: {
				    if(!selectedRow.next(':visible').length)
						selectedRow = tbody.find('tr:first-child');
					else
						selectedRow = selectedRow.next(':visible');
					index = selectedCell.attr("cellIndex");
					cell = selectedRow.find('td:eq('+index+')');
					selectCell(cell);	
				}break;
			}
		}	
		
}
})(jQuery);
