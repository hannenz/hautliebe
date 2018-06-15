<section class="mlog">
	<div class="mlog__overview content-column row__large-column">
	{IF ({ISSET:postsTotal:VAR})}
		{LOOP VAR(posts)}
		<article class="mlog__post clearfix" id="mlog-article-{VAR:id}">
			<header class="mlog__header">
				<p class="mlog__metadata">{VAR:dateDay}. {VAR:dateMonthLongName} {VAR:dateYear} von {VAR:author_firstname} {VAR:author_name} in {VAR:categories}</p>			
				<h1 class="mlog__title">{VAR:post_title}</h1>
				{IF ({ISSET:post_subtitle})}<h2 class="mlog__subtitle">{VAR:post_subtitle}</h2>{ENDIF}
				{IF ({ISSET:post_image:VAR})}
				<figure class="mlog__image">
					<img src="/img/mlog/static/{VAR:post_image}" alt="{VAR:post_title:htmlentities}"  />
				</figure>
				{ENDIF}
			</header>
			<p class="mlog__teaser">
				{VAR:post_teaser}
			</p>
			{IF ({ISSET:post_text:VAR})}
			<div class="mlog__more">
				<a class="button" href="/{PAGELANG}/{PAGEID}/{VAR:linkTitle},{VAR:currentPage},{VAR:categoryId},{VAR:id}.html{IF({ISSET:search:VAR})}?search={VAR:search}{ENDIF}{IF({ISSET:searchIn:VAR})}&amp;searchIn={VAR:searchIn}{ENDIF}">weiterlesen</a>
			</div>
			{ENDIF}
		</article>
		{ENDLOOP VAR}
		<div class="mlog__paging mlog__paging-bottom">{VAR:pagingPrev} {VAR:pagingContent} {VAR:pagingNext}</div>
		{ELSE}
		<h1>Oups, hier war wohl jemand schreibf...</h1>
		<p>In dieser Rubrik existieren im Augenblick leider keine Artikel.</p>
	{ENDIF}
	</div>
	
<!-- 	<aside class="sidebar row__small-column"> -->
<!-- 		<div class="mlog__tags mlog__tags--selectable"> -->
<!-- 			<h3>Tags</h3> -->
<!-- 			<p> -->
<!-- 			{LOOP VAR(tags)} -->
<!-- 			<a class="tag {IF ({IN:{VAR:tag}:{VAR:activeTagsString}})}tag--active{ENDIF}" href="?tag={VAR:tag:htmlentities}">{VAR:tag}</a>		 -->
<!-- 			{ENDLOOP VAR} -->
<!-- 			</p> -->
<!-- 			<p> -->
<!-- 				<a class="button" href="?resetTags=1">alle Tags zurücksetzen</a> -->
<!-- 			</p> -->
<!-- 		</div> -->
<!-- 	</aside> -->
</section>