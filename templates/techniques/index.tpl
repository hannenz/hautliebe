<div class="techniques">
	{LOOP VAR(techniques)}
		<div class="technique">
			<svg class="icon technique__icon"><use xlink:href="#{VAR:technique_icon}"></use></svg>
			<h2 class="headline technique__name">{VAR:technique_name}</h2>
			<div class="technique__description">{VAR:technique_description}</div>
		</div>
	{ENDLOOP VAR}
</div>
