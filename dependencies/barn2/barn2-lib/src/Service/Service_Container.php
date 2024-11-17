<?php

namespace Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Service;

use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Util;
/**
 * A trait for a service container.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 * @version   2.0
 * @internal
 */
trait Service_Container
{
    /**
     * A list of service classes that can be registered by the plugin.
     *
     * @var array
     */
    private array $services_classes = [Core_Service::class, Standard_Service::class, Premium_Service::class];
    /**
     * A list of optional services registered during the plugin bootstrap process, typically when a specific hook is fired.
     *
     * @var array
     */
    private array $services = [];
    /**
     * Add the plugin services to the container.
     *
     * Calls `add_services` to add services classes to the container, and registers a callback for each type of
     * service. Services are only registered with WordPress when the callback is fired. To register the services
     * call `start_services` passing the appropriate `Service` class.
     *
     * @return void
     */
    public final function register_services() : void
    {
        $this->add_services();
        foreach ($this->services_classes as $services_class) {
            \add_action($this->get_services_action_name($services_class), function ($class) {
                $this->register_services_by_class($class);
            });
        }
    }
    /**
     * Get all services.
     *
     * @return array
     */
    public final function get_services() : array
    {
        return $this->services;
    }
    /**
     * Get a service.
     *
     * @param string $id The service ID.
     * @return mixed The service instance.
     */
    public final function get_service($id)
    {
        $services = $this->get_services();
        return $services[$id] ?? null;
    }
    /**
     * Add services to the plugin.
     *
     * This method should be overridden in the client plugin.
     *
     * @return void
     */
    public function add_services()
    {
        // Do nothing here.
    }
    /**
     * Add a service.
     *
     * @param string $id      The service ID.
     * @param mixed  $service The service instance.
     * @return void
     */
    public final function add_service(string $id, Service $service) : void
    {
        if ($this->valid_service_id($id)) {
            $this->services[$id] = $service;
        }
    }
    /**
     * Start all the plugin's services at once.
     */
    public function start_all_services()
    {
        $this->start_core_services();
        $this->start_standard_services();
        $this->start_premium_services();
    }
    /**
     * Start the plugin's core services.
     */
    public function start_core_services()
    {
        $this->start_services(Core_Service::class);
    }
    /**
     * Start the plugin's standard services.
     */
    public function start_standard_services()
    {
        $this->start_services(Standard_Service::class);
    }
    /**
     * Start the plugin's premium services.
     */
    public function start_premium_services()
    {
        $this->start_services(Premium_Service::class);
    }
    /**
     * Get the action name for the registration of a class of services.
     *
     * The action name is generated from the hash of the class name and the service class name.
     * This ensures that each class using the service container registers its own services without conflicts.
     *
     * @param string $service_class The service class name.
     * @return string The action name.
     */
    private function get_services_action_name(string $service_class)
    {
        return \md5(\get_class($this)) . "_register_{$service_class}";
    }
    /**
     * Register the services of a specific class.
     *
     * @param string $class The class name.
     * @return void
     */
    private function register_services_by_class($class) : void
    {
        Util::register_services(\array_filter($this->get_services(), function ($service) use($class) {
            return \is_a($service, $class);
        }));
    }
    /**
     * Fire an action to register a class of services.
     *
     * @param string $service_class The service class name.
     * @return void
     */
    private function start_services(string $service_class) : void
    {
        \do_action($this->get_services_action_name($service_class), $service_class);
    }
    /**
     * Determine whether a service ID is already registered.
     *
     * @param string $id The service ID.
     * @return bool
     */
    private function valid_service_id(string $id)
    {
        return !isset($this->services[$id]);
    }
}
