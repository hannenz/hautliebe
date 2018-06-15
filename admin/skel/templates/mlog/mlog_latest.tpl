{LOOP VAR(posts)}
<div class="widget">
<!-- 	<header class="widget__header"> -->
<!-- 		{VAR:dateDay}.{VAR:dateMonth}.{VAR:dateYear} -->
<!-- 	</header> -->
	<div class="widget__title">{VAR:post_title}</div>
<!-- 	<img class="widget__image" src="" alt="Dummy" /> -->
	<div class="widget__content">
		<p><span class="widget__date">{VAR:dateDay}.{VAR:dateMonth}.{VAR:dateYear}&nbsp;&nbsp;/&nbsp;&nbsp;</span> {VAR:post_teaser}</p>
		<p><a class="button" href="/{PAGELANG}/{VAR:detailsPageId}/{VAR:linkTitle},{VAR:currentPage},{VAR:categoryId},{VAR:id}.html">weiterlesen</a></p>
	</div>
</div>
{ENDLOOP VAR}