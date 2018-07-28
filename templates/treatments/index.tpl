<div class="tabs">
	<nav class="tabs__triggers">
		{LOOP VAR(treatments)}
			<a class="tabs__trigger" href="#treatment-{VAR:id}">
				<span class="tabs__label" title="{VAR:treatment_name}">
				{SWITCH ({VAR:treatment_icon})}
					{CASE (icon_wrinkles)}
						{INCLUDE: PATHTOWEBROOT . "dist/img/icon_wrinkles.svg"}
					{BREAK}
					{CASE (icon_scars)}
						{INCLUDE: PATHTOWEBROOT . "dist/img/icon_scars.svg"}
					{BREAK}
					{CASE (icon_epilation)}
						{INCLUDE: PATHTOWEBROOT . "dist/img/icon_epilation.svg"}
					{BREAK}
				{ENDSWITCH}
				</span>
			</a>
		{ENDLOOP VAR}
	</nav>

	<div class="tabs__panels">
		{LOOP VAR(treatments)}
		<div id="treatment-{VAR:id}" class="tabs__panel">
			<div class="treatment">
				<div class="treatment__header">
					<h4 class="kicker">{VAR:treatment_kicker}</h4>
					<h2 class="headline">{VAR:treatment_name}</h2>
					<div class="treatment__body">{VAR:treatment_description}</div>



					{IF ({ISSET:treatment_faq_question1:VAR} && {ISSET:treatment_faq_answer1:VAR})}
						<div class="tab__additional more">
							<input class="more__trigger" id="cb-1" type="checkbox">
							<label class="more__label" for="cb-1">{VAR:treatment_faq_question1}</label>
							<aside class="more__body">
								{VAR:treatment_faq_answer1}
							</aside>
						</div>
					{ENDIF}
					{IF ({ISSET:treatment_faq_question2:VAR} && {ISSET:treatment_faq_answer2:VAR})}
						<div class="tab__additional more">
							<input class="more__trigger" id="cb-2" type="checkbox">
							<label class="more__label" for="cb-2">{VAR:treatment_faq_question2}</label>
							<aside class="more__body">
								{VAR:treatment_faq_answer2}
							</aside>
						</div>
					{ENDIF}
					{IF ({ISSET:treatment_faq_question3:VAR} && {ISSET:treatment_faq_answer3:VAR})}
						<div class="tab__additional more">
							<input class="more__trigger" id="cb-3" type="checkbox">
							<label class="more__label" for="cb-3">{VAR:treatment_faq_question3}</label>
							<aside class="more__body">
								{VAR:treatment_faq_answer3}
							</aside>
						</div>
					{ENDIF}
				</div>
			</div>
		</div>
		{ENDLOOP VAR}
	</div>
</div>
