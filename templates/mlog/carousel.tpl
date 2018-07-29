<div class="carousel">
	{LOOP VAR(posts)}
	<div class="carousel__slide news-teaser">
		<div class="news-teaser__inner">
			{IF({ISSET:post_image})}
			<figure class="news-teaser__image">
				<img src="/media/mlog/{VAR:post_image}" alt="" />
			</figure>
			{ENDIF}
			<div class="news-teaser__body">
				<h2 class="news-teaser__title title">{VAR:post_title}</h2>
				<h3 class="news-teaser__subheadline subheadline">{VAR:post_subtitle}</h3>
				<div class="news-teaser__text">
					{VAR:post_text}
				</div>
			</div>
			{IF({ISSET:post_eyecatcher})}
				<img class="news-teaser__eyecatcher" src="/media/mlog/{VAR:post_eyecatcher}">
			{ENDIF}
		</div>
	</div>
	{ENDLOOP VAR}
</div>
