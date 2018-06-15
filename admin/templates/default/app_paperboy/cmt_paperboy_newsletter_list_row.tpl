		<div class="formfieldCheckboxOuterContainer alternationColor{VAR:alternationFlag}">
			<div class="formfieldCheckboxContainer">
				<input type="checkbox" class="formInputCheckbox" name="{VAR:selectIdName}[]" value="{VAR:id}" id="{VAR:selectIdName}{VAR:id}"  {IF ({ISSET:newsletterSelected:VAR})}checked="checked"{ENDIF} />
				<label for="{VAR:selectIdName}{VAR:id}">{VAR:newsletter_name}</label>{IF ({ISSET:newsletter_description:VAR})}<p>{VAR:newsletter_description}</p>{ENDIF}
			</div>
		</div>