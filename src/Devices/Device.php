<?php

namespace PushNotifier\Devices;

use PushNotifier\Exceptions\InvalidPlatformException;

class Device
{
    /**
     * @var string The operating system
     */
    protected $operatingSystem;

    /**
     * @var string The device key
     */
    protected $deviceKey;

    /**
     * Set the operating system for the device.
     *
     * @param string $operatingSystem
     *
     * @return Device
     * @throws InvalidPlatformException
     */
    public function setOperatingSystem(string $operatingSystem)
    {
        // Check we support the platform
        if (! in_array(strtolower($operatingSystem), $this->supportedPlatforms())) {
            throw new InvalidPlatformException("Platform {$operatingSystem} is not supported.");
        }

        $this->operatingSystem = strtolower($operatingSystem);

        return $this;
    }

    /**
     * Get the operating system for the device.
     *
     * @return string The operating system
     */
    public function getOperatingSystem()
    {
        return $this->operatingSystem;
    }

    /**
     * Set the device key for the device.
     *
     * @param string $deviceKey
     *
     * @return Device
     */
    public function setDeviceKey(string $deviceKey)
    {
        $this->deviceKey = $deviceKey;

        return $this;
    }

    /**
     * Get the device key for the device.
     *
     * @return string The device key
     */
    public function getDeviceKey()
    {
        return $this->deviceKey;
    }

    /**
     * A list of the platforms supported by the library.
     *
     * @return array The supported platforms
     */
    private function supportedPlatforms()
    {
        return ['ios', 'android'];
    }
}