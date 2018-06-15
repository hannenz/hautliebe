/**
 * ui.treetable.js
 * jQuery-UI-Widget: Erzeugt aus einer Tabelle einen strukturierten Baum
 * 
 * @version 2014-04-10
 * @author J.Hahn <info@content-o-mat.de>
 */
(function(jQuery) {

	jQuery.widget('cmt.treeTable', {
 

		//These options will be used as defaults
		options: {
			treeClass: 'cmtTreeTable',
			nodeClass: 'cmtNode',
			childNodePrefix: 'cmtChildOf-',
			nodePrefix: 'cmtNode-',
			nodeInitializedClass: 'cmtNodeInitialized',
			nodeExpanderClass: 'cmtNodeExpander',
			nodeExpanderActiveClass: 'cmtNodeExpanderActive',
			nodeLoadingClass: 'cmtNodeLoading',
			maxDepth: 0,
			indent: 24,
			nodesInitiallyCollapsed: true,
			saveNodeStatus: true,
			nodesDraggable: true,
			nodeDraggableClass: 'cmtNodeDraggable',
			dropZoneClass: 'cmtDropZone',
			dropZoneBeforeClass: 'cmtDropZoneBefore',
			dropZoneAfterClass: 'cmtDropZoneAfter',
			dropZoneInClass: 'cmtDropZoneIn',
			dropZoneMarkerClass: 'cmtInsertPageMarker',
			droppableAcceptClass: 'cmtDroppableAccept',
			expandedClass: 'cmtNodeExpanded',
			collapsedClass: 'cmtNodeCollapsed',
			rootID: 'root', 
			stringExpand: 'öffnen',
			// ajaxLoad: false,
			ajaxURL: '',
			onStartDrag: function(){},
			onDropOver: function(){},
			onDrop: function() {},
			afterLoadNodes: function() {},
			beforeLoadAjaxUrl: function(parentNode, parsedURL) {return parsedURL;}
		},
		
//		draggableOptions: {
//			helper: 'clone',
//			opacity: 0.9,
//			refreshPositions: true, // Performance?
//			revert: 'invalid',
//			revertDuration: 300,
//			scroll: true,
//			start: this.options.onStartDrag,
//		},
		
		// Container für temporäre Variablen 
		temp: {
			nodeLeftPadding: null
		},
		
		table: null,
		cookiesAvailable: false,
		nodeStatus: {},
		
		/**
		 * function _create()
		 * Methode initalisiert das Widget/ Menü
		 * 
		 * @param void
		 * @return void
		 */
		_create: function() {
			
			var self = this;
			this.table = this.element;

			jQuery(this.table).addClass(this.options.treeClass);

			if (this.options.saveNodeStatus && typeof jQuery.cookie != 'undefined') {
				
				this.cookiesAvailable = true;
				
				this.nodeStatus = this._unParam(jQuery.cookie('cmtNodeStatus'));
				if (typeof this.nodeStatus == 'undefined' || !this.nodeStatus) {
					this.nodeStatus = {};
				}
				
			} else {
				this.options.saveNodeStatus = false;
			}
			
			jQuery('tbody tr', this.table).each( function(index, tr) {
				this.initializeNode(tr);
			}.bindScope(this));
			
			jQuery('tbody tr', this.table).each( function (index, el) {
			//	this._removeInitializationClass(el)
			}.bindScope(this))
			
			
		},
		
		/**
		 * function initializeNode(node)
		 * Initialisiert einen Knoten
		 * 
		 * @param object node Knoten/ DOM-Element
		 * @return void
		 */
		initializeNode: function(node, initializeChildNodes) {
			
			if (typeof initializeChildNodes == 'undefined') {
				initializeChildNodes = true;
			} else {
				initializeChildNodes = false;
			}

			// Knoten als bearbeitet markieren
			if (jQuery(node).hasClass(this.options.nodeInitializedClass)) {
				return;
			} else {
				jQuery(node).addClass(this.options.nodeInitializedClass);
			}
						
			// Einzug berechnen
			var leftPadding = this.getParentsIndentation(node) + this.options.indent;

			this.setIndentation(node, leftPadding);
	
			// erste Tabellenzelle
			var cell = jQuery(node).find('td').first();
			

			// Element für Drag&Drop vorbereiten
			if (this.options.nodesDraggable && !jQuery('.' + this.options.dropZoneClass, node).length) {
				
				var dropPageMarker = '<div class="' + this.options.dropZoneMarkerClass + '"></div>';
				
				var dropZoneBefore = '<div class="' + this.options.dropZoneClass + ' ' + this.options.dropZoneBeforeClass + '">' + dropPageMarker + '</div>';
				jQuery(cell).prepend(dropZoneBefore);
				jQuery('.' + this.options.nodeDraggableClass, cell).addClass(this.options.dropZoneClass + ' ' + this.options.dropZoneInClass);
				
				var dropZoneAfter = '<div class="' + this.options.dropZoneClass + ' ' + this.options.dropZoneAfterClass + '">' + dropPageMarker + '</div>';
				jQuery(cell).append(dropZoneAfter);
				
				jQuery('.' + this.options.dropZoneInClass, cell).append(dropPageMarker)
				
				this.updateNodesDropzone(node);
				this.makeNodeDraggable(node);
			}			

			// Kindknoten lesen
			if (this.options.ajaxURL && parseInt(jQuery(node).data('children'))) {
				var childNodesNr = parseInt(jQuery(node).data('children'));
			} else {
				var childNodes = this.getChildren(node);
				var childNodesNr = childNodes.length;
			}
			
			// Expander für Knoten
			if (childNodesNr) {
				if (!jQuery('.' + this.options.nodeExpanderClass, node).length) {
					var expanderLink = jQuery('<a href="Javascript:void(0);" title="' + this.options.stringExpand + '"  class="' + this.options.nodeExpanderClass + ' ' + this.options.nodeExpanderActiveClass + '"></a>');
					
					jQuery(cell).prepend(expanderLink);
					jQuery(expanderLink).click(this._toggleNodeEvent.bind(this))
				}
			} else {
				jQuery('.' + this.options.nodeExpanderClass, cell).remove();
			}
			
			if (this.options.saveNodeStatus) {

				// Falls Knotenstatus in cookie gespeichert wurde
				if (this.nodeStatus[jQuery(node).attr('id')]) {
					jQuery(node).addClass(this.options.collapsedClass)
					this.expandNode(node);
				} else if (typeof node != 'undefined'){
					this.collapseNode(node);
				}
			} else if (this.options.nodesInitiallyCollapsed) {
				
				// Falls alle Knoten anfangs geschlossen sein sollen 
				this.collapseNode(node);
			}
			// Kind-Elemente des Knotens rekursiv bearbeiten
//			if (initializeChildNodes) {
//				jQuery(childNodes).each(function(index, child) {
//					this.initializeNode(child)
//				}.bindScope(this))
//			}
		},
		
		/**
		 * function _toggleNodeEvent()
		 * Methode wird aufgerufen, wenn auf den Expander eines Knotens geklickt wurde.
		 * 
		 * @param ev jQuery-Eventobjekt 
		 * @return void
		 */
		_toggleNodeEvent: function(ev) {
			
			jQuery(ev.target).blur();
			ev.stopPropagation();
			
			if (!jQuery(ev.target).hasClass(this.options.nodeExpanderActiveClass)) {
				return false;
			}
			
			var clickedNode = jQuery(ev.target).parents('tr')[0];
			
			if (jQuery(clickedNode).hasClass(this.options.collapsedClass)) {
				this.expandNode(clickedNode);
			} else {
				this.collapseNode(clickedNode);
			}
			
		},
		
		/**
		 * function collapseNode()
		 * Schließt einen Knoten
		 * 
		 * @param object node Knoten / DOM-Element der geschlossen werden soll
		 * @return void
		 */
		collapseNode: function(node) {
			if (jQuery(node).hasClass(this.options.collapsedClass)) {
				return;
			}
		
			jQuery(node).addClass(this.options.collapsedClass);
			
			this.setNodeStatus(node, 0);
			this._collapseChildren(node);
		},
		
		/**
		 * function _collapseChildren()
		 * Versteckt auch die Kindelemente, wenn ein Knoten geschlossen wird.
		 * 
		 * @param object parentNode Knoten / DOM-Element der geschlossen wurde (Vaterknoten)
		 */
		_collapseChildren: function(parentNode) {
			
			var children = this.getChildren(parentNode);
			
			jQuery(children).each( function (index, child) {
				jQuery(child).hide();
				this._collapseChildren(child);
			}.bindScope(this))
		},

		/**
		 * function expandNode()
		 * Öffnet einen Knoten
		 * 
		 * @param object node Knoten / DOM-Element der geöffnet werden soll
		 * @return void
		 */
		expandNode: function(node) {
			if (!jQuery(node).hasClass(this.options.collapsedClass)) {
				return;
			}
			
			jQuery(node).removeClass(this.options.collapsedClass);
			
			this.setNodeStatus(node, 1);
			
			this._expandChildren(node);
		},

		/**
		 * function _expandChildren()
		 * zeigt auch die Kindelemente an, wenn ein Knoten geöffnet wird.
		 * 
		 * @param object parentNode Knoten / DOM-Element der geöffnet wurde (Vaterknoten)
		 * 
		 * @return void
		 */
		_expandChildren: function(parentNode) {

			if (this.options.ajaxURL && !jQuery(parentNode).data('childrenLoaded') && jQuery(parentNode).data('children')) {
				this._loadChildren(parentNode);
			}
			
			var children = this.getChildren(parentNode);
			
			jQuery(children).each( function (index, child) {
				jQuery(child).show();
				
				if (!jQuery(child).hasClass(this.options.collapsedClass)) {
					this._expandChildren(child);
				}
			}.bindScope(this))
		},
		
		
		/**
		 * function _loadChildren()
		 * Hilfsmethode: Lädt Kindelemente per Ajax-request nach, falls Parameter options.ajaxURL existiert.
		 * 
		 * @param object parentNode Knoten / DOM-Element dessen Kindelemente nachgeladen werden sollen.
		 * 
		 * @return void
		 */
		_loadChildren: function(parentNode) {
			
			// deavtivate Node Expander temporally
			this.deactivateExpander(parentNode);
			
			jQuery(parentNode).addClass(this.options.nodeLoadingClass);
			
			var parentNodeID = this.getNodeID(parentNode);
			var parsedURL = this.options.ajaxURL.replace(/\{nodeID\}/, parentNodeID);
			
			parsedURL = this.options.beforeLoadAjaxUrl(parentNode, parsedURL);
			
			jQuery.ajax({
				url: parsedURL,
				complete: function(response, status) {
					
					if (status == 'success') {
						this.addChildren(parentNodeID, response.responseText);
					}
				}.bindScope(this)
			})
			
		},
		
		/**
		 * function addChildren()
		 * Fügt neue Kindelemente zu einem Knoten hinzu.
		 * 
		 * @param number nodeID Knoten-ID als Zahl (z.B. aus der Datenbank)
		 * @param mixed children Kindknoten entweder als HTML oder als Objekt (noch nicht implementiert!)
		 * 
		 * @return void
		 */
		addChildren: function(nodeID, children) {
			
			var argType = (typeof children).toLowerCase();
			var jqNodeID = '#' + this.options.nodePrefix + nodeID;
			
			switch(argType) {
				
				// TODO?
				case 'object':
					
					break;
					
				default:
					jQuery(jqNodeID).after(children);
					break;
			}

			// prevent re-adding children
			jQuery(jqNodeID).data('childrenLoaded', true);
			
			// initialize newly added children
			jQuery(this.getChildren(jqNodeID)).each(function(index, node) {
				this.initializeNode(node)
			}.bindScope(this))
			
			// reactivate node expander
			this.activateExpander(nodeID);
			
			// remove loading Class
			jQuery(jqNodeID).removeClass(this.options.nodeLoadingClass);
			
			// Event: After insert
			this.options.afterLoadNodes(children, jqNodeID);
		},
		
		/**
		 * function activateExpander()
		 * Aktiviert einen zuvor inaktiven Knotenexpander ([+]).
		 *  
		 * @param object node Knoten / DOM-Element dessen Expander aktiviertn.
		 * 
		 * @return void
		 */
		activateExpander: function(node) {
			
			jQuery(this.getExpander(node)).addClass(this.options.nodeExpanderActiveClass);
		},

		/**
		 * function deactivateExpander()
		 * Deaktiviert einen zuvor aktiven Knotenexpander ([+]).
		 *  
		 * @param object node Knoten / DOM-Element dessen Expander deaktiviert werden soll.
		 * 
		 * @return void
		 */
		deactivateExpander: function(node) {
			
			jQuery(this.getExpander(node)).removeClass(this.options.nodeExpanderActiveClass);
		},
		
		/**
		 * function getExpander()
		 * Liefert den Expander eines Knotens zurück.
		 *  
		 * @param object node Knoten / DOM-Element dessen Expander gefunden werden soll.
		 * 
		 * @return object Knoten / DOM-Element
		 */
		getExpander: function(node) {
			return jQuery(node).andSelf().find('.' + this.options.nodeExpanderClass);
		},
		
		/**
		 * function makeNodeDraggable()
		 * Initialisiert einen Knoten, so dass er per Drag'n'Drop bewegt werden kann.
		 * 
		 * @param object node Knoten / DOM-Element der bewegt werden soll
		 * 
		 * @return void
		 */
		makeNodeDraggable: function(node) {
			
			// Configure draggable nodes
			jQuery('.' + this.options.nodeDraggableClass, node).draggable({
				helper: 'clone',
				opacity: 0.9,
				refreshPositions: true, // Performance?
				revert: 'invalid',
				revertDuration: 300,
				scroll: true,
				start: this.options.onStartDrag,
			});
		
			jQuery('.' + this.options.dropZoneClass).each( function(index, el) {

				var widget = this;
				
				jQuery(el).droppable({
					accept: '.' + widget.options.nodeDraggableClass,
					addClasses: false,
					drop: function(droppable, ui) {

						// User-Methode 
						if (typeof widget.options.onDropOver == 'function') {
							widget.options.onDropOver(droppable, ui);
						}
						
						// Widget
						var draggedNode = jQuery(ui.draggable).parents('tr')[0];
						var targetNode = jQuery(droppable.target).parents('tr')[0];
						var targetDropZone = jQuery(droppable.target)
						var targetDepth = widget.getDepth(targetNode);

						if (jQuery(targetDropZone).hasClass(widget.options.dropZoneInClass)) {
							targetDepth += 1
						}

						// Falls Knoten in sich selbst oder in ein Kindelement kopiert werden soll, dann abbrechen!
						if (widget.isAncestor(targetNode, draggedNode)) {
							return false;
						}
						
						// Asttiefe testen
						if (widget.options.maxDepth && targetDepth > widget.options.maxDepth) {
							return false;
						}

						// Gezogenen Knoten an neuer Position einfügen.
						if (jQuery(droppable.target).hasClass(widget.options.dropZoneBeforeClass)) {
							
							//var targetNode = widget.getParent(targetNode);
							widget.moveNode(draggedNode, targetNode, 'before');
							
						} else if (jQuery(droppable.target).hasClass(widget.options.dropZoneAfterClass)) {
						
							//var targetNode = widget.getParent(targetNode);
							widget.moveNode(draggedNode, targetNode, 'after');
						
						} else {
							widget.moveNode(draggedNode, targetNode, 'in');
						}
						
						//widget.refreshAlternation();
					},
					hoverClass: widget.options.droppableAcceptClass,
					over: function(e, ui) {

						// User-Methode
						if (typeof widget.options.onDrop == 'function') {
							widget.options.onDrop(e, ui);
						}
						
						// Widget
						var targetNode = jQuery(e.target).parents('tr')[0];
						var draggedNode = jQuery(ui.draggable).parents('tr')[0];
						var targetDropZone = jQuery(e.target)
						
						var targetDepth = widget.getDepth(targetNode);
						if (jQuery(targetDropZone).hasClass(widget.options.dropZoneInClass)) {
							targetDepth += 1
						}

						// Testen, ob Knoten nicht über sich selbst oder ein Kindelement gezogen wird.
						if (widget.isAncestor(targetNode, draggedNode) 
							|| (widget.options.maxDepth && targetDepth > widget.options.maxDepth)) {
							jQuery(ui.helper).addClass('cmtDroppableReject');
						} else {
							jQuery(ui.helper).removeClass('cmtDroppableReject');
						}
						
						// Baumknoten öffnen, wenn geschlossen und Kindelemente vorhanden
						if (!jQuery.isEmptyObject(widget.getChildren(targetNode)) && jQuery(targetNode).hasClass(widget.options.collapsedClass)) {
							widget.expandNode(targetNode)
						}

					}.bindScope(widget)
				});
			}.bindScope(this));
		},
		
		/**
		 * function moveNode()
		 * Verschiebt einen Knoten mitsamt aller Kindelemente an eine andere Stelle im Baum.
		 * 
		 * @param object sourceNode Knoten der verschoben werden soll.
		 * @param object targetNode Zielknoten.
		 * @param string position Zielposition: 
		 * "before" => Knoten wird auf die gleiche Ebene vor den Zielknoten positioniert
		 * "after" => Knoten wird auf die Ebene des Zielknotens aber danach positioniert
		 * "in" => Knoten wird zu Kindelement des Zielknotens.
		 */
		moveNode: function(sourceNode, targetNode, position) {
			
			// Falls ein Kindknoten an ein anderes Element gehängt wird, muss der ehemalige Vaterknoten reinitialisiert werden (könnte leer sein)
			var parentNode = this.getParent(sourceNode);
			
			switch(position) {
				
				case 'before':
					var levelID = this.getParentNodeID(sourceNode);
					var lastNode = jQuery(sourceNode).siblings('.'+ this.options.childNodePrefix + levelID).last();

					jQuery(sourceNode).insertBefore(targetNode);
					
					this.changeChildClass(sourceNode, this.options.childNodePrefix + this.getNodeID(this.getParent(targetNode)));
					this.setIndentation(sourceNode, this.getIndentation(targetNode))
					
					this.updateNodesDropzone(sourceNode);
					this.updateNodesDropzone(lastNode);
					
					targetNode = this.getParent(targetNode);
					break;
					
				case 'after':

					// Wenn Element Kindelemente hat, dann das letzte Element suchen und Knoten dort anhängen
					var sourceNode = jQuery(sourceNode).detach()
					var endNode = this.getBranchsEndNode(targetNode);
					
					jQuery(sourceNode).insertAfter(endNode);

					this.changeChildClass(sourceNode, this.options.childNodePrefix + this.getNodeID(this.getParent(targetNode)));
					this.setIndentation(sourceNode, this.getIndentation(targetNode))

					this.updateNodesDropzone(sourceNode);
					this.updateNodesDropzone(targetNode);
					break;

				case 'in':
				default:

					var levelID = this.getParentNodeID(sourceNode)
					var lastNode = jQuery(sourceNode).siblings('.'+ this.options.childNodePrefix + levelID).last();
					jQuery(sourceNode).insertAfter(targetNode);
					
					this.changeChildClass(sourceNode, this.options.childNodePrefix + this.getNodeID(targetNode));
					this.setIndentation(sourceNode, this.getIndentation(targetNode) + this.options.indent);
				
					this.updateNodesDropzone(sourceNode);
					this.updateNodesDropzone(lastNode);
					
					break;
			}

			// Kindelemente ebenfalls verschieben
			this.moveChildNodes(sourceNode, sourceNode);
	
			// Ehemaligen Vaterknoten initialisieren
			jQuery(parentNode).data('children', this.getChildrenNr(parentNode));
			this._removeInitializationClass(parentNode);
			this.initializeNode(parentNode, false);
				
			// Ziel-Knoten initialisieren
			jQuery(parentNode).data('children', this.getChildrenNr(targetNode));
			this._removeInitializationClass(targetNode);
			this.initializeNode(targetNode, false);

			
		},
		
		/**
		 * function moveChildNodes()
		 * Verschiebt alle Kindknoten eines Knotens an eine neue Stelle im Baum.
		 * 
		 * @param object sourceNode Knoten / DOM-Element Knoten, dessen Kindelemente verschoben werden sollen.
		 * @param object targetNode Knoten / DOM-Element Zielknoten
		 * 
		 * @return void
		 */
		moveChildNodes: function(sourceNode, targetNode) {
			
			var childNodes = this.getChildren(sourceNode);
	
			jQuery(childNodes).reverse().each( function(index, childNode) {
				
				jQuery(childNode).insertAfter(targetNode);
				this.setIndentation(childNode, this.getIndentation(targetNode) + this.options.indent);
				this.changeChildClass(childNode, this.options.childNodePrefix + this.getNodeID(targetNode));
				
				// rekursiv anwenden
				this.moveChildNodes(childNode, childNode);
				
			}.bindScope(this));
			
		},
		
		/**
		 * function getBranchsEndNode()
		 * Ermittelt rekursiv das letzte "<tr>" des Vaterknotens (inkl. aller Kindknoten). Wird benötigt, falls das Element, hinter welches ein neuer Knoten gehängt werden soll, Kindelemente hat. 
		 * 
		 * @param object node Knoten / DOM-Element
		 * @returns object Letzter Knoten / letztes DOM-Element des Vaterknotens
		 */
		getBranchsEndNode: function(node) {

			var children = this.getChildren(node);
	
			if (children.length) {
				var endChildNode = jQuery(this.getChildren(node)).last();
				return this.getBranchsEndNode(endChildNode)
			} else {
				return node
			}
		},
		
		/**
		 * function updateNodesDropzone()
		 * Blendet die Dropzone nach dem Knotenelement ein, wenn der Knoten der letzte in einer Reihe ist, damit andere Elemente auch hinter dem Knoten platziert werden können.
		 * 
		 * @param object node Knoten / DOM-Element
		 * @return void
		 */
		updateNodesDropzone: function(node) {

			if (!this.isLastNode(node)) {
				var display = 'none';
			} else {
				var display = 'block';
			}
				
			jQuery(node, this.table).find('.' + this.options.dropZoneAfterClass).first().css({
				display: display
			});
		},
		
		/**
		 * function isLastNode()
		 * Ermittelt, ob der übergebene Knoten der letzte in der Reihe ist.
		 * 
		 * @param object node Knoten / DOM-Element
		 * @returns {Boolean}
		 */
		isLastNode: function(node) {
			if (jQuery(this.getSiblings(node).last()).attr('id') == jQuery(node).attr('id')) {
				return true;
			} else {
				return false;
			}
			
		},

		/**
		 * function changeChildClass()
		 * Passt die CSS-Klasse des Knotens an die ID des neuen Vaterknotens an (entfernt alten CSS-Selektor "cmtChildOf-..." und ersetzt ihn durch einen neuen.
		 *  
		 * @param object node Knoten / DOM-Element
		 * @param string selector Neuer Selektor. Achtung: Der definierte Prefix-String wird diesem Selektor vorangestellt. Normalerweise ist der Selektor die ID des Vaterelements! 
		 */
		changeChildClass: function(node, selector) {

			// Klassennamen des gezogenen Elements anpassen
			var regExp = new RegExp(this.options.childNodePrefix + '[0-9a-z]+');

			var className = jQuery(node).attr('class')
			className = className.replace(regExp, '');
			
			jQuery(node).attr('class' , className += ' ' + selector);
		},

		/**
		 * function getParentNodeID(
		 * Ermittelt die ID (Zahl!) des Vaterknotens
		 * 
		 * @param object node DOM-Element/ Knoten
		 * @return string ID-Teil aus dem class-Attribut des Elements
		 */
		getParentNodeID: function(node) {
			var regExp = new RegExp(this.options.childNodePrefix + '([0-9a-z\-]+)');
			var nodeClass = jQuery(node).attr('class');
			
			if (nodeClass) {
				var match = nodeClass.match(regExp);
				if (match) {
					return match[1]
				}
			}
			return '';
		},
		
		/**
		 * function getIndentation()
		 * Ermittelt den linken Einzug des Knotens.
		 * 
		 * @param object node Knoten / DOM-Element
		 * @returns number Einzug in Pixeln.
		 */
		getIndentation: function(node) {
			var indentation = parseInt(jQuery(node).find('td').first().css('padding-left'));
			
			if (indentation) {
				return indentation; 
			} else {
				return 0
			}
		},
		/**
		 * function setIndentation()
		 * Setzt den Einzug eines Knotens.
		 * 
		 * @param object node Knoten / DOM-Element
		 * @param number indentation Zahlenwert des Einzugs. Nicht "24px" angeben sondern "24"
		 * 
		 * @returns {Boolean}
		 */
		setIndentation: function(node, indentation) {
			
			if (isNaN(indentation)) {
				return false;
			}
			
			jQuery(node).find('td').first().css({
				paddingLeft: indentation + 'px'
			})
		},

		/**
		 * function getParentsIndentation()
		 * Ermittelt den linken Einzug des Vaterknotens.
		 * 
		 * @param object node Knoten / DOM-Element
		 * 
		 * @returns number Einzug in Pixeln.
		 */		
		getParentsIndentation: function(node) {
			return this.getIndentation(this.getParent(node));
		},
		
		/**
		 * function getSiblings()
		 * Ermittelt alle Elemente auf der selben Ebene von "node"
		 * 
		 * @param object node Knoten / DOM-Element
		 * @returns object Liste aller Elemente auf der selben Ebene
		 */
		getSiblings: function(node) {
			var parentNodeID = this.getParentNodeID(node);
			return jQuery('.' + this.options.childNodePrefix + parentNodeID, this.table);
		},
		
		/**
		 * function isAncestor()
		 * Prüft, ob ein Knoten ein Kind eines anderen Knotens ist, also ob beide miteinander verwandt sind.
		 * 
		 * @param object childNode Kind-Knoten / DOM-Element
		 * @param object parentNode Vater-Knoten / DOM-element
		 * @returns boolean
		 */
		isAncestor: function(childNode, parentNode) {
			
			do {
				var childNodeID = this.getNodeID(childNode);
				var parentNodeID = this.getNodeID(parentNode);
				
				if (childNodeID == 'root') {
					return false;
				}
				
				if (childNodeID == parentNodeID) {
					return true;
				}
				
				childNode = this.getParent(childNode);
			} while (childNode)
				
			return false;
			
		},
		
		/**
		 * function getParent()
		 * Ermittelt den Vaterknoten des Elements
		 * 
		 * @param object node Knoten / DOM-Element
		 * @returns object Vaterlement
		 */
		getParent: function(node) {
			var parentNodeID = this.getParentNodeID(node);
			return jQuery('#' + this.options.nodePrefix + parentNodeID, this.table)[0]
		},
		
		/**
		 * function getParents()
		 * Ermittelt alle Elternelemente eines Knotens bis zur Hauptebene.
		 * 
		 * @param object node Knoten / DOM-Element
		 * @returns array Array mit allen Eltern eines Knotens
		 */
		getParents: function(node) {
			var parents = new Array();
			var parent = this.getParent(node);
			
			while (!jQuery.isEmptyObject(parent)) {
				parents.push(parent);
				parent = this.getParent(parent);
			}
			
			return parents;
		},
		
		/**
		 * function getDepth()
		 * Ermittelt die "Tiefe" eines Knotens in der Baumstruktur.
		 * 
		 * @param object node Knoten / DOM-Element
		 * @returns number Tiefe des Knotens als Zahl (0 = Root-Ebene)
		 */
		getDepth: function(node) {
			var parents = this.getParents(node); 
			return parents.length;
		},
		
		/**
		 * function getChildren()
		 * Ermittelt alle Kindelemente des Knotens
		 * 
		 * @param object node Knoten / DOM-Element
		 * @returns object Liste aller Kindelemente auf der selben Ebene
		 */
		getChildren: function(node) {
			var nodeID = this.getNodeID(node);
			return jQuery('.' + this.options.childNodePrefix + nodeID, this.table);
		},
		
		/**
		 * function getChildrenNr()
		 * Liefert die Anzahl der Kindelemente eines Knotens
		 * 
		 * @param object node Knoten / DOM-Element
		 * @returns number Anzahl der Kindelemente
		 */
		getChildrenNr: function(node) {
			return this.getChildren().length;
		},
		
		/**
		 * function getNodeID();
		 * Ermittelt die ID-Nummer(!) eines Knotens
		 * 
		 * @param object node DOM-Element/ Knoten
		 * @return string ID-Teil aus dem id-Attribut des Elements
		 */
		getNodeID: function(node) {
			var nodeID = jQuery(node).attr('id');
			
			if (nodeID) {
				var regExp = new RegExp(this.options.nodePrefix);
				var id = jQuery(node).attr('id').replace(regExp, '');
				
				if (id) {
					return id;
				} else {
					return this.options.rootID;
				}
			} else {
				return this.options.rootID;
			}
		},
		
		/**
		 * function setNodeStatus()
		 * Speichert den Sichtbarkeitszustand eines Knotens in einem internen Objekt und optional auch in einem Cookie.
		 * 
		 * @param object node DOM-Element/ Knoten
		 * @param boolean status true = Knoten geöffnet, false = Knoten geschlossen
		 * 
		 * @return void
		 */
		setNodeStatus: function(node, status) {
			
			this.nodeStatus[jQuery(node).attr('id')] = status;

			if (this.options.saveNodeStatus) {
				var nodesStatus = jQuery.param(this.nodeStatus);
				jQuery.cookie('cmtNodeStatus', nodesStatus);
			}
		},
		
		
		/**
		 * function _removeInitializationClass()
		 * Hilfsfunktion: Entfernt den bei der Initialisierung eines Knotens hinzugefügten CSS-Selektor
		 * 
		 * @param object node DOM-Element/ Knoten
		 * 
		 * @return void
		 */
		_removeInitializationClass: function(node) {
			jQuery(node).removeClass(this.options.nodeInitializedClass);
		},
		
		
		/**
		 * function serializeNodes()
		 * Erstellt aus der Baumstruktur ein multidimensionales Objekt, welches die Struktur des Baumes abbbildet.
		 * 
		 * @param string rootNodeID ID des Vaterknotens als Ausgangspunkt für die Struktur
		 * @param object serializedNodes Objekt: Wird vom rekursiven Aufruf übergeben und kann daher anfangs weggelassen werden.
		 * 
		 * @return object Multidimensionales Object
		 */
		serializeNodes: function(rootNodeID, serializedNodes) {

			if (typeof rootNodeID == 'undefined') {
				var rootNodeID = this.options.rootID
			}
	
			if (typeof serializedNodes == 'undefined') {
				var serializedNodes = {}
			}
var pos = 1;
			jQuery('tr.' + this.options.childNodePrefix + rootNodeID, this.table).each( function (index, node) {

				var nodeID = this.getNodeID(node);
				var parentID = this.getParentNodeID(node);
				
				nodeObject = {
					'position': pos,
					'id': nodeID,
					'parentID': parentID,
					'children': this.serializeNodes(nodeID)
				}
				serializedNodes[pos++] = nodeObject;
				//serializedNodes["'" + nodeID.toString() + "'"] = this.serializeNodes(nodeID)
			}.bindScope(this))
			
			if (jQuery.isEmptyObject(serializedNodes)) {
				return {}
			} else {
				return serializedNodes;
			}
		},
		
		
		/**
		 * function _unParam()
		 * Erzeugt aus einem mit jQuery.param() serializierten String ein Objekt.
		 * 
		 * @param string string zuvor serialisierte Zeichenkette
		 * @param string delimiter Trennzeichen zwischen den Variable/Wert-Paaren (default:&)
		 * 
		 * @returns object Javascript-Objekt
		 */
		_unParam: function(string, delimiter) {

			if (typeof string != 'string') {
				return [];
			}
			
			if (typeof delimiter == 'undefined') {
				var delimiter = '&';
			}
			
			var pairs = string.split(delimiter);
			var objString = '';
			var newPairs = new Array();
			
			for (a in pairs) {
				var pairParts = pairs[a].split('=');

				if (pairParts[0]) {
					newPairs.push("'" + pairParts[0] + "':" + pairParts[1]); //pairs[a].replace(/=/, ':');
				}
			}

			eval ("var objString = {" + newPairs.join(',') + "}");
			
			return objString;
		},
		
		
		refreshAlternation: function() {
			return;
			jQuery('tr', this.table).each(function (index, el) {
				jQuery(el).removeClass('cmtAlternate' + (index+1)%2);
				jQuery(el).addClass('cmtAlternate' + index%2);
			})
		}
	});
}(jQuery));