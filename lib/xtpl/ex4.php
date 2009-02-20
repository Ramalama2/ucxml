<?php

/**
 * example 4
 * demonstrates recursive parse
 *
 * @package XTemplate_Examples
 * @author Barnabas Debreceni [cranx@users.sourceforge.net]
 * @copyright Barnabas Debreceni 2000-2001
 * @author Jeremy Coates [cocomp@users.sourceforge.net]
 * @copyright Jeremy Coates 2002-2007
 * @see license.txt LGPL / BSD license
 * @link $HeadURL$
 * @version $Id$
 */

	include_once('./xtemplate.class.php');

	$xtpl = new XTemplate('ex4.xtpl');
	$xtpl->rparse('main');
	$xtpl->out('main');

?>