<a class="mlog__paginglink prev-page" href="/{PAGELANG}/{VAR:pageId}/{IF({ISSET:categoryName:VAR})}Rubrik-{VAR:categoryName}{ELSE}Themen{ENDIF},{VAR:prevPage},{VAR:categoryId}.html{IF({ISSET:search:VAR})}?search={VAR:search}{ENDIF}{IF({ISSET:searchIn:VAR})}&amp;searchIn={VAR:searchIn}{ENDIF}">&lt;</a> 
{SPLITTEMPLATEHERE}
<a class="mlog__paginglink {IF({ISSET:selected:VAR})}mlog__paginglink-selected{ENDIF}" href="/{PAGELANG}/{VAR:pageId}/{IF({ISSET:categoryName:VAR})}Rubrik-{VAR:categoryName}{ELSE}Themen{ENDIF},{VAR:listItem},{VAR:categoryId}.html{IF({ISSET:search:VAR})}?search={VAR:search}{ENDIF}{IF({ISSET:searchIn:VAR})}&amp;searchIn={VAR:searchIn}{ENDIF}">{VAR:listItem}</a> 
{SPLITTEMPLATEHERE}
<a class="mlog__paginglink next-page" href="/{PAGELANG}/{VAR:pageId}/{IF({ISSET:categoryName:VAR})}Rubrik-{VAR:categoryName}{ELSE}Themen{ENDIF},{VAR:nextPage},{VAR:categoryId}.html{IF({ISSET:search:VAR})}?search={VAR:search}{ENDIF}{IF({ISSET:searchIn:VAR})}&amp;searchIn={VAR:searchIn}{ENDIF}">&gt;</a> 
{SPLITTEMPLATEHERE}
<a class="mlog__paginglink first-page" href="/{PAGELANG}/{VAR:pageId}/News-Aktuelles-{VAR:firstPage}.html?cp={VAR:firstPage}">erste Seite</a>
{SPLITTEMPLATEHERE}
<a class="mlog__paginglink last-page" href="/{PAGELANG}/{VAR:pageId}/News-Aktuelles-{VAR:lastPage}.html?cp={VAR:lastPage}">letzte Seite</a>