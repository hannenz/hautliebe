<?php
namespace Contentomat;

/**
 * This Parser can be extended by own (project-specific) macros etc.
 */
class AppParser extends Parser {

	/**
	 * Returns the amount of items in an array
	 * 
	 * @param string $value Content
	 * @param array $params void
	 *
	 * @return string 	String representation of the number of items in this array
	 */
	public function macro_COUNT ($value, $params) {

		if (!isset($this->vars[$value])) {
			$replaceData = '0';
		}
		else if (!is_array($this->vars[$value])) {
			$replaceData = '0';
		}
		else {
			$replaceData = sprintf('%s', count((array)$this->vars[$value]));
		}

		return $replaceData;
	}

	/**
	 * Generates a munged Email-Link, so that the email address is unreadable for robots
	 * but readable for humans and decodes on click back to the original email
	 * 
	 * e.g. MUNGEDEMAILLINK(info@example.com)
	 * 
	 * will output
	 * 
	 * <a href="javascript:void(0)" onclick="stufftodecodethemungedemail">info<span>@</span>example<span>.</span>com</a>
	 * 
	 * Needs Javascript enabled
	 * 
	 * @param string $value email address to munge
	 * @param array $params 'mungeTag' => the Tag name to use for munging, default: span
	 *
	 * @return string 	Complete link tag with munged email and decoder onclick 
	 */
	public function macro_MUNGEDEMAILLINK ($email, $params) {

		$mungeTag = !empty($params['mungeTag']) ? $params['mungeTag'] : 'span';

		$mungedEmail = preg_replace('/\./', "<{$mungeTag}>.</{$mungeTag}>", $email);
		$mungedEmail = preg_replace('/(@)/', "<{$mungeTag}>@</{$mungeTag}>", $mungedEmail);
		$onclick = sprintf('(function(e, obj) { obj.setAttribute(\'href\', \'mailto:\' + obj.innerHTML.replace(/(\<\/?%s\>)/g, \'\')); return true; })(event, this);', $mungeTag);

		return sprintf('<a href="javascript:void(0);" onclick="%s">%s</a>', $onclick, $mungedEmail);
	}

}



