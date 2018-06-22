{IF({LAYOUTMODE})}
<label>Tab Nr. (1-5)</label>
<div>{HEAD:3}</div>
<div class="">
{ELSE}
<div class="tab treatment" id="tab-{HEAD:3}">
{ENDIF}
	<header class="treatment__header">
		<h4 class="kicker">{HEAD:1}</h4>
		<h2 class="headline">{HEAD:2}</h2>
	</header>
	<div class="treatment__body">
		{HTML:1}
	</div>
	{IF(!{LAYOUTMODE})}
		{IF({ISSET:text1:CONTENT})}
			<div class="tab__additional more">
				<input class="more__trigger" id="cb-{HEAD:3}-1" type="checkbox">
				<label class="more__label" for="cb-{HEAD:3}-1">{HEAD:4}</span></label>
				<aside class="more__body">
					{TEXT:1}
				</aside>
			</div>
		{ENDIF}
		{IF({ISSET:text2:CONTENT})}
			<div class="tab__additional more">
				<input class="more__trigger" id="cb-{HEAD:3}-2" type="checkbox">
				<label class="more__label" for="cb-{HEAD:3}-2">{HEAD:5}</span></label>
				<aside class="more__body">
					{TEXT:2}
				</aside>
			</div>
		{ENDIF}
	{ELSE}
		<div>
			<label>Zusätzlicher Text 1</label>
			<p>{HEAD:4}</p>
			<aside>
				{TEXT:1}
			</aside>
		</div>
		<div>
			<label>Zusätzlicher Text 2</label>
			<p>{HEAD:5}</p>
			<aside>
				{TEXT:2}
			</aside>
		</div>
	{ENDIF}
</div>
