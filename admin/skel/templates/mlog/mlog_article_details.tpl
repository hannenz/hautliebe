<section class="mlog">
	<div class="mlog__details content-column row__large-column">
		<article class="mlog__post">
			<header class="mlog__header">
<!-- 				<p class="mlog__metadata">{VAR:dateDay}. {VAR:dateMonthLongName} {VAR:dateYear} von {VAR:author_firstname} {VAR:author_name} in {VAR:categories}</p>			 -->
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
			<div class="mlog__text">
				{VAR:post_text}
			</div>
			{IF({ISSET:hasImage:VAR})}
			<div class="mlog__media mlog__media--bottom">
				<h3>Screenshots</h3>
				<div class="cards">
				{LOOP VAR(postImage)}
					<div class="card">
						<a class="colorBoxImages" href="/img/mlog/{VAR:media_internal_filename}" title="{VAR:media_title:htmlentities}" >
							<figure class="card__image">
								<img src="/img/mlog/thumbnails/{VAR:media_internal_filename}" alt="{VAR:media_title:htmlentities}" />
							</figure>
							{IF ({ISSEt:media_title:VAR})}<div class="card__content">{VAR:media_title:htmlentities}</div>{ENDIF}
						</a>
					</div>				
				{ENDLOOP VAR}
				</div>
			</div>
			{ENDIF}

		<!-- 	<div id="socialshareprivacyDetails" class="clearfix"></div> -->
			
			<div class="mlog__service">
				<a class="button" href="/{PAGELANG}/{PAGEID}/{IF({ISSET:categoryName:VAR})}{VAR:categoryName}{ELSE}Uebersicht{ENDIF},{VAR:currentPage},{VAR:categoryId}.html{IF({ISSET:search:VAR})}?search={VAR:search}{ENDIF}{IF({ISSET:searchIn:VAR})}&amp;searchIn={VAR:searchIn}{ENDIF}#{VAR:id}" class="linkBack">zurück zur Übersicht</a>
			</div>
		</article>
	</div>
	<aside class="sidebar row__small-column">
		{IF ({ISSET:hasTags:VAR})}
		<div class="mlog__tags">
			<h3>Tags</h3>
			{LOOP VAR(postTags)}
			<span class="tag">{VAR:tag}</span>		
			{ENDLOOP VAR}
		</div>
<!-- 		<div class="widget mlog__tags"> -->
<!-- 			<h3 class="widget__title">Tags</h3> -->
<!-- 			<div class="widget__content"> -->
<!-- 			{LOOP VAR(postTags)} -->
<!-- 			<span class="tag">{VAR:tag}</span>		 -->
<!-- 			{ENDLOOP VAR} -->
<!-- 			</div> -->
<!-- 		</div> -->
		{ENDIF}
		{IF ({ISSET:hasRelatedPosts:VAR})}
		<div class="mlog__relatedposts">
			<h3>Verwandte Artikel</h3>
			{LOOP VAR(relatedPosts)}
			<a href="/{PAGELANG}/{PAGEID}/{VAR:linkTitle},{VAR:currentPageNr},{VAR:categoryId},{VAR:id}.html{IF({ISSET:search:VAR})}?search={VAR:search}{ENDIF}{IF({ISSET:searchIn:VAR})}&amp;searchIn={VAR:searchIn}{ENDIF}">{VAR:post_title}</a>		
			{ENDLOOP VAR}
		</div>
<!-- 		<div class="widget mlog__relatedposts"> -->
<!-- 			<h3 class="widget__title">Verwandte Artikel</h3> -->
<!-- 			<div class="widget__content"> -->
<!-- 			{LOOP VAR(relatedPosts)} -->
<!-- 			<a href="/{PAGELANG}/{PAGEID}/{VAR:linkTitle},{VAR:currentPage},{VAR:categoryId},{VAR:id}.html{IF({ISSET:search:VAR})}?search={VAR:search}{ENDIF}{IF({ISSET:searchIn:VAR})}&amp;searchIn={VAR:searchIn}{ENDIF}">{VAR:post_title}</a>		 -->
<!-- 			{ENDLOOP VAR} -->
<!-- 			</div> -->
<!-- 		</div> -->
		{ENDIF}	
	</aside>
</section>
