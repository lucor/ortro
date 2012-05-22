<?php
    /**
     * 
     * DESCRIPTION: 
     * 
     * PHP version 5
     * 
     * file name  : SimpleTestActions.inc.php
     * created    : Wed 19 Oct 2005 11:12:35 AM PDT
     * 
     * @category 
     * @package 
     * @author Nimish Pachapurkar <npac@spikesource.com>
     * @copyright Copyright (C) 2004-2006 SpikeSource, Inc.
     * @license http://www.spikesource.com/license.html Open Software License v2.1
     * @version $Revision: $
     * @link 
     *
     * modifications:
     *
     */

     $SIMPLETEST_ACTIONS = array(
	"wait-for-ms" => array(
            "*" => array(
                "*" => array(
                    "function" => "sleep",
                    "params" => 1
                )
            )
        ),
        "goto" => array(
            "window.location" => array(
                "*" => array(
                    "function" => "get",
                    "params" => 1
                )
            ),
            "*" => array(
                "*" => array(
                    "function" => "get",
                    "params" => 1
                )
            )
        ),
        "verify-link-href" => array(
            // No way to verify link href as of now
            // just verify label
            "A" => array(
                "CDATA" => array(
                    "function" => "assertLink",
                    "params" => 1
                ),
                "ID" => array(
                    "function" => "assertLinkById",
                    "params" => 1
                )
            )
        ),
        "verify-link-text" => array(
            "A" => array(
                "CDATA" => array(
                    "function" => "assertLink",
                    "params" => 1
                ),
                "ID" => array(
                    "function" => "assertLinkById",
                    "params" => 1
                )
            )
        ),
        "click" => array(
            "A" => array(
                "CDATA" => array(
                    "function" => "clickLink",
                    "params" => 1
                ),
                "ID" => array(
                    "function" => "clickLinkById",
                    "params" => 1
                )
            ),
            "INPUT" => array(
                "VALUE" => array(
                    "function" => "click",
                    "params" => 1
                ),
                "ID" => array(
                    "function" => "setFieldById",
                    "params" => 2
                ),
                "NAME" => array(
                    "function" => "setFieldByName",
                    "params" => 2
                )
            )
        ),
        "verify-title" => array(
            "*" => array(
                "*" => array(
                    "function" => "assertTitle",
                    "params" => 1
                )
            )
        ),
        "fill" => array(
            "INPUT" => array(
                "ID" => array(
                    "function" => "setFieldById",
                    "params" => 2
                ),
                "NAME" => array(
                    "function" => "setFieldByName",
                    "params" => 2
                ),
                "VALUE" => array(
                    "function" => "setField",
                    "params" => 2
                )
            ),
            "TEXTAREA" => array(
                "ID" => array(
                    "function" => "setFieldById",
                    "params" => 2
                ),
                "NAME" => array(
                    "function" => "setFieldByName",
                    "params" => 2
                )
            )
        ),
        "select" => array(
            "SELECT" => array(
                "ID" => array(
                    "function" => "setFieldById",
                    "params" => 2
                ),
                "NAME" => array(
                    "function" => "setFieldByName",
                    "params" => 2
                )
            )
        ),
        "check" => array(
            "INPUT" => array(
                "NAME" => array(
                    "function" => "setFieldByName",
                    "params" => 2
                ),
                "ID" => array(
                    "function" => "setFieldById",
                    "params" => 2
                ),
                "VALUE" => array(
                    "function" => "setField",
                    "params" => 2
                )
            )
        ),
        "verify-button-value" => array(
            "INPUT" => array(
                "VALUE" => array(
                    "function" => "assertField",
                    "params" => 1
                )
            )
        ),
        "assert-text-exists" => array(
            "*" => array(
                "*" => array(
                    "function" => "assertText",
                    "params" => 1
                )
            )
        ),
        "assert-text-not-exists" => array(
            "*" => array(
                "*" => array(
                    "function" => "assertNoText",
                    "params" => 1
                )
            )
        )
    );
?>
