<?php

namespace Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib;

/**
 * An object which is loaded conditionally.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 * @internal
 */
interface Conditional
{
    /**
     * Is this object required?
     *
     * @return boolean true if required, false otherwise.
     */
    public function is_required();
}
