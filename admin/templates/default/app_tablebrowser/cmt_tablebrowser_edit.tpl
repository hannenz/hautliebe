<script type="text/javascript">
 function showCollations(charset) {
 	var menu = document.getElementById('cmt_collation');
 	var optgroups = menu.getElementsByTagName('optgroup');
 	var c = 0;
 	while (optgroups[c]) {
 		if (optgroups[c].title != charset) {
 			optgroups[c].disabled = 'disabled';
 			optgroups[c].readonly = 'readonly';
 			optgroups[c].style.display = 'none';
			changeStatus(optgroups[c].getElementsByTagName('option'), true, 'hidden');
			changeClass(optgroups[c].getElementsByTagName('option'), 'tabbrowserDropdownItemDisabled');
 		} else {
			// Internet Explorer ignorriert das alles!
 			optgroups[c].disabled = '';
 			optgroups[c].readonly = '';
 			optgroups[c].style.display = 'block';
 			var optionElements = optgroups[c].getElementsByTagName('option');
 			optionElements[0].selected = true;
			changeClass(optionElements, 'tabbrowserDropdownItem');
			changeStatus(optionElements, false, 'show');
 		}
 		c++;
 	}
 }
 
 function changeClass(refElements, newClass) {
 	var c = 0;
 	while (refElements[c]) {
 		refElements[c].className = newClass
 		c++;
 	}
 }

 function changeStatus(refElements, stat, vis) {
 	var c = 0;
 	while (refElements[c]) {
 		refElements[c].disabled = stat
 		c++;
 	}
 }
</script>
{VAR:content}
{IF ({ISSET:charset:VAR})}<script type="text/javascript">showCollations('{VAR:charset}');</script>{ENDIF}