<div id="cmt-menu-handle" class="cmt-clearfix">
	<span class="cmt-flat-button cmt-menu-icon"></span>
	<span id="cmt-logo">
		<span class="cmt-logo-char">c</span>
		<span class="cmt-logo-shrink">ontent-o-</span>
		<span class="cmt-logo-char">m</span>
		<span class="cmt-logo-shrink">a</span>
		<span class="cmt-logo-char">t</span>
		<span class="cmt-logo-version">&nbsp;v{VAR:cmtVersion}</span>	
	</span>
</div>

<section id="cmt-panel-main">
	<section id="cmt-messages"></section>
	<section id="cmt-panels" class="cmt-dynamic-container">

		<section id="cmt-panel-new-object" class="cmt-panel">
			<h4>Neues Objekt</h4>
			<div id="cmt-select-object-container">
				<div class="cmt-new-object"></div>
				<select id="cmt-select-object">
					{VAR:selectObject}
				</select>
			</div>
			<h4>Seite</h4>
			<script>selfURL = '{SELFURL}&{PAGEID}&{PAGELANG}';</script>
			<p class="cmt-data-row">
				<label for="cmt-data-page-title">Seitentitel</label>
				<input id="cmt-data-page-title" name="cmtPageTitle" class="cmt-page-data" value="{VAR:cmtPageTitle}" type="text" />  
			</p>
			<p class="cmt-data-row">
				<label for="cmt-data-page-visibility">Sichtbarkeit</label>
				<select id="cmt-data-page-visibility" name="cmtPageVisibility" class="cmt-page-data">
					<option value="1" {IF ("{VAR:cmtPageVisibility}" == "1")}selected="selected"{ENDIF}>sichtbar</option>
					<option value="0" {IF ("{VAR:cmtPageVisibility}" == "0")}selected="selected"{ENDIF}>unsichtbar</option>
					<option value="99" {IF ("{VAR:cmtPageVisibility}" == "99")}selected="selected"{ENDIF}>gesperrt</option>
				</select>  
			</p>
			<p class="cmt-panel-main-buttons clearfix">
				<a href="?sid={SID}&amp;cmtApplicationID={CONSTANT:CMT_REFERINGPAGE}&amp;cmtTab={CONSTANT:CMT_SLIDER}&cmtLanguage={PAGELANG}">
					<button title="zur&uuml;ck zum Content-o-mat" class="cmt-button cmt-button-no-text cmt-button-back">
						<span class="cmt-button-icon"></span>
					</button>
				</a>
				<button id="cmt-preview-page" title="Vorschau" class="cmt-button cmt-button-preview">
					<span class="cmt-button-icon"></span>
					<span class="cmt-button-text">Vorschau</span>
				</button>
				<button id="cmt-save-page" title="Seite speichern" class="cmt-button cmt-button-save">
					<span class="cmt-button-icon"></span>
					<span class="cmt-button-text">speichern</span>
				</button>
			</p>
		</section>
		
		<!-- panel: object -->
		<section id="cmt-panel-object" class="cmt-panel">
			<h4>Objekt</h4>
			<p class="cmt-panel-main-buttons cmt-clearfix">
				<button class="cmt-button cmt-button-no-text cmt-button-abort">
					<span class="cmt-button-icon"></span>
				</button>
				<button title="Bild speichern" class="cmt-button cmt-button-save cmt-button-save-image">
					<span class="cmt-button-icon"></span>
					<span class="cmt-button-text">speichern</span>
				</button>
			</p>
			<label for="cmt-data-object-template-id">Objekttyp &auml;ndern</label>
			<select id="cmt-data-object-template-id">
				{VAR:changeObject}
			</select>
		</section>
		
		<!-- panel: image -->
		<section id="cmt-panel-image" class="cmt-panel cmt-dynamic-container">
			<h4>Bild</h4>
			<p class="cmt-panel-main-buttons cmt-clearfix">
				<button class="cmt-button cmt-button-no-text cmt-button-abort">
					<span class="cmt-button-icon"></span>
				</button>
				<button title="Bild speichern" class="cmt-button cmt-button-save cmt-button-save-image">
					<span class="cmt-button-icon"></span>
					<span class="cmt-button-text">speichern</span>
				</button>
			</p>
			<section id="cmt-subpanel-image-select" class="cmt-subpanel cmt-dynamic-container">
				<div id="cmt-images-content-container" class="cmt-dynamic-container cmt-dynamic-content" ></div>
				<p class="cmt-data-row">
					<label for="cmt-data-image-alt-text">Bildtitel</label>
					<input id="cmt-data-image-alt-text" name="cmt-data-image-alt-text" type="text" />  
				</p>
				<p class="cmt-data-row">
					<label for="cmt-data-image-add-html">zusätzliches HTML (optional)</label>
					<input id="cmt-data-image-add-html" name="cmt-data-image-add-html" type="text" /> 
				</p>
				<input type="hidden" name="cmt-data-image-path" id="cmt-data-image-path" value="" />
				<input type="hidden" name="cmt-data-image-base-path" id="cmt-data-image-base-path" value="" />
			</section>
		</section>

		<!-- panel: links -->
		<section id="cmt-panel-link" class="cmt-panel cmt-dynamic-container">
			<h4>Link</h4>
			<p class="cmt-panel-main-buttons cmt-clearfix">
				<button class="cmt-button cmt-button-no-text cmt-button-abort">
					<span class="cmt-button-icon"></span>
				</button>
				<button title="Link speichern" class="cmt-button cmt-button-save cmt-button-save-link">
					<span class="cmt-button-icon"></span>
					<span class="cmt-button-text">speichern</span>
				</button>
<!-- 				<button class="cmt-button-abort">abbrechen</button> -->
<!-- 				<button class="cmt-button-save-link">Bild speichern</button> -->
			</p>
			<p class="cmt-data-row">
				<label for="cmt-data-link-type">Linktyp</label>
				<select id="cmt-data-link-type" name="cmt-data-link-type" class="cmt-select-subpanel">
					<option value="internal" data-subpanel-id="cmt-subpanel-link-internal">interner Link</option>
					<option value="external" data-subpanel-id="cmt-subpanel-link-external">externer Link</option>
					<option value="download" data-subpanel-id="cmt-subpanel-link-download">Download-Datei</option>
					<option value="email" data-subpanel-id="cmt-subpanel-link-email">E-Mail</option>
					<option value="individual" data-subpanel-id="cmt-subpanel-link-individual">individuell</option>
				</select>
			</p>
			
			<!-- internal links -->
			<section id="cmt-subpanel-link-internal" class="cmt-subpanel cmt-dynamic-container">
				<p class="cmt-data-row">
					<label for="cmt-data-internal-language">Sprachversion</label>
					<select id="cmt-data-internal-language" name="cmt-data-internal-language">
						{LOOP VAR(cmtLanguages)}
							<option value="{VAR:languageShortcut}">{VAR:languageName}</option>
						{ENDLOOP VAR}
					</select>
				</p>
				<input id="cmt-data-internal-page-id" name="cmt-data-internal-page-id" type="hidden" />
				<label for="cmt-pages-content-container">Seite</label>
				<div id="cmt-pages-content-container" class="cmt-dynamic-container cmt-dynamic-content"></div>
				<p class="cmt-data-row">
					<label for="cmt-data-internal-add-html">zusätzliches HTML (optional)</label>
					<input id="cmt-data-internal-add-html" name="cmt-data-internal-add-html" type="text" />  
				</p>
			</section>

			<!-- external link -->
			<section id="cmt-subpanel-link-external" class="cmt-subpanel">
				<p class="cmt-data-row">
					<label for="cmt-data-external-url">Adresse (URL)</label>
					<input id="cmt-data-external-url" name="cmt-data-external-url" type="text" />  
				</p>
				<p class="cmt-data-row">
					<label for="cmt-data-external-target">Zielfenster</label>
					<select id="cmt-data-external-target" name="cmt-data-external-target">
						<option value="">gleiches Fenster (Standard)</option>
						<option value="_blank">neues Fenster (_blank)</option>
					</select>
				</p>
				<p class="cmt-data-row">
					<label for="cmt-data-external-add-html">zusätzliches HTML (optional)</label>
					<input id="cmt-data-external-add-html" name="cmt-data-external-add-html" type="text" />  
				</p>
			</section>

			<!-- download link -->
			<section id="cmt-subpanel-link-download" class="cmt-subpanel cmt-dynamic-container">
				<p class="cmt-data-row">
					<label for="cmt-data-download-target">Zielfenster</label>
					<select id="cmt-data-download-target" name="cmt-data-download-target">
						<option value="">gleiches Fenster (Standard)</option>
						<option value="_blank">neues Fenster (_blank)</option>
					</select>
				</p>
				<label for="cmt-directory-content-container">Datei</label>
				<div id="cmt-directory-content-container" class="cmt-dynamic-container cmt-dynamic-content"></div>
				<p class="cmt-data-row">
					<label for="cmt-data-download-add-html">zusätzliches HTML (optional)</label>
					<input id="cmt-data-download-add-html" name="cmt-data-download-add-html" type="text" /> 
					<input id="cmt-data-download-url" name="cmt-data-download-url" type="hidden" /> 
				</p>
			</section>

			<!-- email link -->
			<section id="cmt-subpanel-link-email" class="cmt-subpanel">
				<p class="cmt-data-row">
					<label for="cmt-data-email">E-Mailadresse</label>
					<input id="cmt-data-email" name="cmt-data-email" type="text" />
				</p>
				<p class="cmt-data-row">
					<label for="cmt-data-email-add-html">zusätzliches HTML (optional)</label>
					<input id="cmt-data-email-add-html" name="cmt-data-email-add-html" type="text" />  
				</p>
			</section>

			<!-- individual -->
			<section id="cmt-subpanel-link-individual" class="cmt-subpanel">
				<p class="cmt-data-row">
					<label for="cmt-data-individual-url">Anweisung für href-Attribut</label>
					<input id="cmt-data-individual-url" name="cmt-data-individual-url" type="text" />
				</p>
				<p class="cmt-data-row">
					<label for="cmt-data-individual-target">Zielfenster</label>
					<select id="cmt-data-individual-target" name="cmt-data-individual-target">
						<option value="">gleiches Fenster (Standard)</option>
						<option value="_blank">neues Fenster (_blank)</option>
					</select>
				</p>
				<p class="cmt-data-row">
					<label for="cmt-data-individual-add-html">zusätzliches HTML (optional)</label>
					<input id="cmt-data-individual-add-html" name="cmt-data-individual-add-html" type="text" />  
				</p>
			</section>
		</section>
		
		<!-- panel: script -->
		<section id="cmt-panel-script" class="cmt-panel cmt-dynamic-container">
			<h4>Script</h4>
			<p class="cmt-panel-main-buttons cmt-clearfix">
				<button class="cmt-button cmt-button-no-text cmt-button-abort">
					<span class="cmt-button-icon"></span>
				</button>
				<button title="Auswahl &uuml;bernehmen" class="cmt-button cmt-button-save cmt-button-save-script">
					<span class="cmt-button-icon"></span>
					<span class="cmt-button-text">speichern</span>
				</button>
			</p>
			<section id="cmt-script-select" class="cmt-dynamic-container">
				<div id="cmt-scripts-content-container" class="cmt-dynamic-container cmt-dynamic-content"></div>
				<input type="hidden" name="cmt-data-script-path" id="cmt-data-script-path" value="" />
				<input type="hidden" name="cmt-data-script-base-path" id="cmt-data-script-base-path" value="" />
			</section>
		</section>
	</section>
</section>

<div id="cmt-message-contents" lang="de">
	<div class="cmt-message-page-saved">Die Seite wurde erfolgreich gespeichert.</div>
	<div class="cmt-message-page-not-saved">Die Seite konnte nicht gespeichert werden.</div>
</div>
