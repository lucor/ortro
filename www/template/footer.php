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

$poweredby = 'Powered by <a href="http://www.ortro.net/" target="_blank">' .
             'Ortro</a> - v' . ORTRO_VERSION .
             ' - <a href="http://dev.ortro.net/" ' .
             'target="_blank">Bug/feature request</a>';   
?>

<div class="push"></div>
</div>
<!--  End wrapper div -->
<!--  Start ortro-footer div -->
<div class="footer">
<p>
<?php 
if ($auth->isAuthorized()) {  
    echo $poweredby;
} else {
    echo $poweredby; 
    ?>
    <br/><br/>
    <img src="img/valid-xhtml10.png" alt="Valid XHTML 1.0!" 
         title="Valid XHTML 1.0!" border="0" 
         height="15" width="80"/>
    <img src="img/valid-css.png" alt="Valid CSS!" title="Valid CSS!" 
         border="0" height="15" width="80"/>    
    <?php 
} 
?>
</p>
</div>
<!--  End ortro-footer div -->
</body>
</html>