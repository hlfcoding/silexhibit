<?php define('SITE', 'Bonjour!');

/* Indexhibit BackEnd Execution Process */

// -----------------------------------------------------------
// 	WELCOME TO INDEXHIBIT
//
//  A collaboration between Daniel Eatock and Tatiret, c.o.
//  Open source and free to use for good in this world.
//
//  If we've missed a credit, or you simply deserve more
//  credit today, just let us know. Thank you!!!
//
//  An MVC without the V. Or with a small v. ;)
//  Thank you for shopping at indexhibit.org
// -----------------------------------------------------------

require_once 'bootstrap.php';
require_once 'defaults.php';
if (file_exists('config/config.php')) {
    require_once 'config/config.php';
}
require_once 'common.php';
	
// preloading things
load_helpers(array('html', 'entrance', 'time', 'server'));

// general tools for loading things
load_class('core', FALSE, 'lib');

// "I'm digging for fire" - Pixies	
$OBJ =& load_class('router', TRUE, 'lib');

// are we logged in?
$OBJ->access->checkLogin();

// get user prefernces
$OBJ->lang->setlang($OBJ->access->prefs['user_lang']);

// loading our module object
$INDX =& load_class($go['a'], TRUE, 'mod', TRUE);

// referencing wonkiness
// review when there is time
$aINDX =& $INDX;

// loading our module method
$OBJ->tunnel($aINDX, $go['a'], $go['q']);

// output
$INDX->template->output('index');
