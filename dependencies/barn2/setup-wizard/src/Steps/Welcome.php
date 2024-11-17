<?php

/**
 * @package   Barn2\setup-wizard
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Setup_Wizard\Steps;

use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Setup_Wizard\Step;
use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Setup_Wizard\Util;
/**
 * Handles the welcome step of the wizard.
 * Displays a license validation field and validates the license.
 * @internal
 */
class Welcome extends Step
{
    /**
     * Initialize the step.
     */
    public function __construct()
    {
        $this->set_id('welcome');
        $this->set_name(esc_html__('Welcome', 'barn2-setup-wizard'));
    }
    /**
     * {@inheritdoc}
     */
    public function setup_fields()
    {
        return [];
    }
    /**
     * {@inheritdoc}
     */
    public function submit($values)
    {
        return Api::send_success_response();
    }
}
