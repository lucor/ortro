<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Footer Template
 * 
 * PHP version 5
 * 
 * LICENSE: This file is part of Ortro.
 * Ortro is published under the terms of the GNU GPL License v2 
 * Please see LICENSE and COPYRIGHT files for details.
 *
 * @category GUI
 * @package  Ortro
 * @author   Luca Corbo <lucor@ortro.net>
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */

$poweredby = 'Powered by <a href="http://www.ortro.net/" target="_blank">Ortro</a>'. 
             ' - v' . ORTRO_VERSION . 
             ' - <a href="http://sourceforge.net/tracker/?group_id=173809" ' . 
             'target="_blank">Bug/feature request</a>';   
?>

<div class="push"></div>
</div>
<!--  End wrapper div -->
<!--  Start ortro-footer div -->
<div class="footer">
<p>
<?php echo $poweredby; ?>
</p>
</div>
<!--  End ortro-footer div -->
</body>
</html>