<form name="editentryform" action="{SELFURL}" method="POST" enctype="application/x-www-form-urlencoded">
<div class="editentryHeadWithIcon">
<div class="editentryHeadIcon"><img src="{CMT_TEMPLATE}app_languages/img/icon_new_language.gif"></div>
{VAR:head}
</div>
{VAR:user_message}
<div class="editentry_subhead">Sprachangaben</div>
<div class="editentry_alternate0" id="cmt_languagename"><span class="editentry_entryhead">Sprache (Bezeichnung)</span><br>{VAR:cmt_languagename}<br>{VAR:cmt_languagename_desc}</div>
<div class="editentry_alternate1" id="cmt_language"><span class="editentry_entryhead">Sprache (Abkürzung)</span><br>{VAR:cmt_language}<br>{VAR:cmt_language_desc}</div>
<div class="editentry_alternate0" id="cmt_charset"><span class="editentry_entryhead">Zeichensatz</span><br>{VAR:cmt_charset}<br>{VAR:cmt_charset_desc}</div>
<div class="editentry_subhead">Struktur und/oder Inhalte kopieren</div>
<div class="editentry_alternate0" id="cmt_charset">
<input type="radio" name="cmt_copy" value="all" checked>&nbsp;Seiten und Seiteninhalte kopieren<br>
<input type="radio" name="cmt_copy" value="pagesonly">&nbsp;nur Seiten (erstellt leere Seiten)<br>
<input type="radio" name="cmt_copy" value="none">&nbsp;weder Seiten noch Seiteninhalte kopieren (erstellt leere Sprachversion)<br>&nbsp;<br>
Inhalte und/oder Struktur kopieren von Sprachversion:<br>
{VAR:select_language}
</div>
<div class="editentry_subhead">Sonstiges</div>
<div class="editentry_alternate1" id="cmt_position"><span class="editentry_entryhead">Position der Sprachversion in der Strukturübersicht</span><br>{VAR:cmt_position}<br>{VAR:cmt_position_desc}</div>

<div class="editentry_service"><a href="{SELFURL}&amp;action="><img src="{CMT_TEMPLATE}img/icon_back.gif" border="0"></a>&nbsp;&nbsp;<input src="{CMT_TEMPLATE}img/icon_save.gif" name="save" value="1" type="image" style="margin: 0px;"></div>
{VAR:hidden_fields}
</form>