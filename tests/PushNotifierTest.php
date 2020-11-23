<?php

use Dotenv\Dotenv;
use PushNotifier\Devices\Device;
use PushNotifier\Devices\Devices;
use PushNotifier\Exceptions\DevicesNotSetException;
use PushNotifier\Exceptions\InvalidCertificateException;
use PushNotifier\Exceptions\InvalidPlatformException;
use PushNotifier\Exceptions\PushNotificationNotSetException;
use PushNotifier\PushNotification;
use PHPUnit\Framework\TestCase;
use PushNotifier\Server;

class PushNotifierTest extends TestCase
{
    /**
     * Perform setup.
     */
    protected function setUp(): void
    {
        // load sensitive stuff from .env - only used in testing
        // see .env.example for example file
        (Dotenv::createImmutable(__DIR__ . '/../'))->load();
    }

    /**
     * Create a device.
     *
     * @param string $operatingSystem The operating system
     * @param string $deviceKey       The device key
     *
     * @return Device The created device.
     * @throws InvalidPlatformException
     */
    private function createDevice($operatingSystem = "iOS", $deviceKey = "test-ios-device-key")
    {
        return (new Device())
            ->setOperatingSystem($operatingSystem)
            ->setDeviceKey($deviceKey);
    }
    /**
     * Make sure we can build a valid push notification.
     *
     * @test
     */
    public function aPushNotificationCanBeBuilt()
    {
        // Create a Push Notification
        $notification = (new PushNotification())
            ->setTitle('Test notification title')
            ->setContent('Test notification content');

        // Make sure it's present
        $this->assertInstanceOf(PushNotification::class, $notification);
        $this->assertEquals('Test notification title', $notification->getTitle());
        $this->assertEquals('Test notification content', $notification->getContent());
    }

    /**
     * Make sure we can create a device.
     *
     * @test
     */
    public function aDeviceCanBeCreated()
    {
        // Create the device
        $iosDevice = (new Device())
            ->setOperatingSystem('ios')
            ->setDeviceKey('test-ios-device-key');

        // Ensure the data is valid
        $this->assertEquals('ios', $iosDevice->getOperatingSystem());
        $this->assertEquals('test-ios-device-key', $iosDevice->getDeviceKey());

        // Create the device
        $androidDevice = (new Device())
            ->setOperatingSystem('android')
            ->setDeviceKey('test-android-device-key');

        // Ensure the data is valid
        $this->assertEquals('android', $androidDevice->getOperatingSystem());
        $this->assertEquals('test-android-device-key', $androidDevice->getDeviceKey());
    }

    /**
     * We should only allow platforms that we support.
     *
     * @test
     */
    public function onlySupportValidPlatforms()
    {
        // We're expecting an exception here.
        $this->expectException(InvalidPlatformException::class);

        // Set an invalid device
        $invalidDevice = (new Device())
            ->setOperatingSystem('invalid');
    }

    /**
     * Make sure we can build a device collection.
     *
     * @test
     * @throws InvalidPlatformException
     */
    public function canBuildACollectionOfDevices()
    {
        // Create devices collection
        $devices = new Devices([
            $this->createDevice(),
            $this->createDevice('Android', 'android-key'),
        ]);

        // Assert it's a device collection
        $this->assertInstanceOf(Devices::class, $devices);

        // Assert it's got 2 devices assigned
        $this->assertCount(2, $devices->all());
    }

    /**
     * Make sure we can set devices on the server object.
     * @test
     */
    public function canSetDevicesOnServer()
    {
        // Create devices collection
        $devices = new Devices([
            $this->createDevice(),
            $this->createDevice('Android', 'android-key'),
        ]);

        // Create a server object
        $server = (new Server())
            ->setDevices($devices);

        // Check we have the right objects
        $this->assertInstanceOf(Server::class, $server);
        $this->assertInstanceOf(Devices::class, $server->getDevices());
    }


    /**
     * Make sure we can set certain parameters.
     *
     * @test
     * @throws InvalidCertificateException
     */
    public function canSetServerParameters()
    {
        // Create a server object with iOS-only params
        $iosServer = (new Server())
            ->setApnsCertificate($_ENV["APNS_CERTIFICATE_PATH"])
            ->setApnsTeamId($_ENV["APNS_TEAM_ID"])
            ->setApnsKeyId($_ENV["APNS_KEY_ID"]);

        // Make some checks
        $this->assertInstanceOf(Server::class, $iosServer);
        $this->assertEquals($_ENV["APNS_CERTIFICATE_PATH"], $iosServer->getApnsCertificate());
        $this->assertEquals($_ENV["APNS_TEAM_ID"], $iosServer->getApnsTeamId());
        $this->assertEquals($_ENV["APNS_KEY_ID"], $iosServer->getApnsKeyId());

        // Create a server object with Android-only params
        $androidServer = (new Server())
            ->setFcmCertificate($_ENV["FCM_CERTIFICATE_PATH"]);

        // Make some checks
        $this->assertInstanceOf(Server::class, $androidServer);
        $this->assertEquals($_ENV["FCM_CERTIFICATE_PATH"], $androidServer->getFcmCertificate());
    }

    /**
     * Ensure an exception is thrown if the APNS certificate path given does not exist.
     *
     * @test
     */
    public function invalidApnsCertificatePathThrowsException()
    {
        // We're expecting an InvalidCertificateException error.
        $this->expectException(InvalidCertificateException::class);
        $this->expectExceptionMessage("The APNS certificate path /definitely/not/a/valid/path does not exist.");

        // Create a server object with iOS-only params
        $iosServer = (new Server())
            ->setApnsCertificate("/definitely/not/a/valid/path");
    }

    /**
     * Ensure an exception is thrown if the FCM certificate path given does not exist.
     *
     * @test
     */
    public function invalidFcmCertificatePathThrowsException()
    {
        // We're expecting an InvalidCertificateException error.
        $this->expectException(InvalidCertificateException::class);
        $this->expectExceptionMessage("The FCM certificate path /definitely/not/a/valid/path does not exist.");

        // Create a server object with iOS-only params
        $androidServer = (new Server())
            ->setFcmCertificate("/definitely/not/a/valid/path");
    }

    /**
     * Make sure we can set the notification on the server.
     *
     * @test
     */
    public function canSetNotificationOnServer()
    {
        // Create a Push Notification
        $notification = (new PushNotification())
            ->setTitle('Test notification title')
            ->setContent('Test notification content');

        // Create a server object
        $server = (new Server())
            ->setPushNotification($notification);

        // Make some assertions
        $this->assertInstanceOf(Server::class, $server);
        $this->assertInstanceOf(PushNotification::class, $server->getPushNotification());
        $this->assertEquals("Test notification title", $server->getPushNotification()->getTitle());
        $this->assertEquals("Test notification content", $server->getPushNotification()->getContent());
    }

    /**
     * We should only be able to send push notifications if we have Devices set on the Server.
     * @test
     */
    public function cannotSendAPushNotificationWithoutDevices()
    {
        $this->expectException(DevicesNotSetException::class);

        // Create a Push Notification
        $notification = (new PushNotification())
            ->setTitle('Test notification title')
            ->setContent('Test notification content');

        // Create a server object
        $server = (new Server())
            ->setPushNotification($notification);

        // Send the notification
        $server->send();
    }

    /**
     * We should only be able to send push notifications if we have a PushNotification set on the Server.
     * @test
     */
    public function cannotSendAPushNotificationWithoutPushNotification()
    {
        $this->expectException(PushNotificationNotSetException::class);

        // Create a Push Notification
        $devices = (new Devices(
            $this->createDevice()
        ));

        // Create a server object
        $server = (new Server())
            ->setDevices($devices);

        // Send the notification
        $server->send();
    }

    /**
     * We need to make sure that we're processing devices correctly.
     *
     * @test
     */
    public function pushNotificationsSendCorrectly()
    {

    }
}