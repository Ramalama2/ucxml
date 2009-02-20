<?php

/**
 * example 9
 * Tag Callback
 *
 * @package XTemplate_Examples
 * @author Jeremy Coates [cocomp@users.sourceforge.net]
 * @copyright Jeremy Coates 2007
 * @see license.txt BSD license
 * @link $HeadURL: https://xtpl.svn.sourceforge.net/svnroot/xtpl/trunk/ex8.php $
 * @version $Id: ex8.php 21 2007-05-29 18:01:15Z cocomp $
 */

require_once('xtemplate.class.php');

class XTemplate_subclass extends XTemplate {

	public function my_custom_callback ($tag_contents) {

		$tag_contents = '<span style="text-decoration: underline;">' . $tag_contents . '</span>';

		return $tag_contents;
	}
}

$xtpl = new XTemplate_subclass('ex9.xtpl', '', null, 'main', false);
// Change delimiters as we want to display {tag} tags for demo
$xtpl->tag_start_delim = '{{';
$xtpl->tag_end_delim = '}}';
$xtpl->setup();

$xtpl->assign('example1', 'Some text for example 1');
$xtpl->assign('example2', 'Some text for example 2');
$xtpl->assign('example3', 'Some text for example 3');
$xtpl->assign('example4', 'Some text for example 4');
$xtpl->assign('example5', 'Some text for example 5');

// Show the default callback functions
foreach ($xtpl->allowed_callbacks as $callback) {

	$xtpl->assign('callback', $callback);
	$xtpl->parse('main.callback');
}

$xtpl->parse('main');
$xtpl->out('main');

?>