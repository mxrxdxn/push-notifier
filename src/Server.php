<?php

namespace PushNotifier;

use PushNotifier\Devices\Devices;
use PushNotifier\Exceptions\DevicesNotSetException;
use PushNotifier\Exceptions\InvalidCertificateException;
use PushNotifier\Exceptions\PushNotificationNotSetException;

class Server
{
    /**
     * @var PushNotification The push notification
     */
    protected $pushNotification;

    /**
     * @var Devices The devices
     */
    protected $devices;

    /**
     * @var string The APNS certificate path
     */
    protected $apnsCertificate = "";

    /**
     * @var string The APNS Team ID
     */
    protected $apnsTeamId = "";

    /**
     * @var string The APNS Key ID
     */
    protected $apnsKeyId = "";

    /**
     * @var string The FCM certificate path
     */
    protected $fcmCertificate = "";

    /**
     * Set the devices for the notification.
     *
     * @param Devices $devices
     *
     * @return Server
     */
    public function setDevices(Devices $devices)
    {
        $this->devices = $devices;

        return $this;
    }

    /**
     * Get the devices for the notification.
     *
     * @return Devices
     */
    public function getDevices()
    {
        return $this->devices;
    }

    /**
     * Set the path to the APNS certificate.
     *
     * @param string $certificatePath
     *
     * @return $this
     * @throws InvalidCertificateException
     */
    public function setApnsCertificate(string $certificatePath)
    {
        // Throw an exception if the file doesn't exist
        if (! file_exists($certificatePath)) {
            throw new InvalidCertificateException("The APNS certificate path {$certificatePath} does not exist.");
        }

        $this->apnsCertificate = $certificatePath;

        return $this;
    }

    /**
     * Get the path to the APNS certificate.
     *
     * @return string
     */
    public function getApnsCertificate()
    {
        return $this->apnsCertificate;
    }

    /**
     * Set the APNS Team ID.
     *
     * @param string $teamId
     *
     * @return $this
     */
    public function setApnsTeamId(string $teamId)
    {
        $this->apnsTeamId = $teamId;

        return $this;
    }

    /**
     * Get the APNS Team ID.
     *
     * @return string
     */
    public function getApnsTeamId()
    {
        return $this->apnsTeamId;
    }

    /**
     * Set the APNS Key ID.
     *
     * @param string $keyId
     *
     * @return $this
     */
    public function setApnsKeyId(string $keyId)
    {
        $this->apnsKeyId = $keyId;

        return $this;
    }

    /**
     * Get the APNS Key ID.
     *
     * @return string
     */
    public function getApnsKeyId()
    {
        return $this->apnsKeyId;
    }

    /**
     * Set the path to the FCM certificate.
     *
     * @param string $certificatePath
     *
     * @return $this
     * @throws InvalidCertificateException
     */
    public function setFcmCertificate(string $certificatePath)
    {
        // Throw an exception if the file doesn't exist
        if (! file_exists($certificatePath)) {
            throw new InvalidCertificateException("The FCM certificate path {$certificatePath} does not exist.");
        }

        $this->fcmCertificate = $certificatePath;

        return $this;
    }

    /**
     * Get the path to the FCM certificate.
     *
     * @return string
     */
    public function getFcmCertificate()
    {
        return $this->fcmCertificate;
    }

    /**
     * Set the push notification.
     *
     * @param PushNotification $pushNotification
     *
     * @return $this
     */
    public function setPushNotification(PushNotification $pushNotification)
    {
        $this->pushNotification = $pushNotification;

        return $this;
    }

    /**
     * Get the push notification.
     *
     * @return PushNotification
     */
    public function getPushNotification()
    {
        return $this->pushNotification;
    }

    /**
     * Send the push notification.
     *
     * @throws DevicesNotSetException
     * @throws PushNotificationNotSetException
     */
    public function send()
    {
        // If the devices aren't set, we need to throw an exception.
        if ($this->getDevices() === null) {
            throw new DevicesNotSetException("The push server does not have any devices set.");
        }

        // If there's no notification set, we need to throw an exception.
        if ($this->getPushNotification() === null) {
            throw new PushNotificationNotSetException("The push server does not have a push notification set.");
        }
    }
}