{IF({LAYOUTMODE})}
<label>Tab Nr. (1-3)</label>
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
</div>

