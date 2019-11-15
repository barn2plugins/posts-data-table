<?php

namespace Barn2\Lib;

/**
 * 
 */
interface Service_Provider {

    /**
     * Get the service for the specified ID.
     *
     * @param string $id The service ID
     * @return Service
     */
    public function get_service( $id );

}
