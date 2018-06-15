
#### content-o-mat start ####
# content-o-mat: Mod Rewrite: Achtung! Die URL wird auf die Datei index5.php umgeschrieben. Im Live-Betrieb bitte auf index.php ändern
RewriteEngine on
RewriteBase {REWRITE_BASE}
RewriteRule ^([^/]*)\/([0-9]+)\/.*\.html$ index5.php?pid=$2&lang=$1&%{QUERY_STRING}&%{REQUEST_URI}

# content-o-mat: *.inc, *.tpl (Templates) und .htaccess Dateien nicht anzeigen
<FilesMatch "\.(htaccess|inc|tpl)$">
order deny,allow
deny from all
</FilesMatch>
#### content-o-mat end ####