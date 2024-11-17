<?php

/**
 * @package   Barn2\setup-wizard
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Setup_Wizard\Interfaces;

/** @internal */
interface Restartable
{
    /**
     * Send data back to the react app when the wizard is restarted.
     *
     * @return void
     */
    public function on_restart();
}
