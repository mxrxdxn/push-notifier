<?php

namespace PushNotifier;

class PushNotification
{
    /**
     * @var $title string The notification title
     */
    protected $title = "";

    /**
     * @var $content string The notification content
     */
    protected $content = "";

    /**
     * Set the notification title.
     *
     * @param $title string The title of the push notification
     *
     * @return PushNotification
     */
    public function setTitle(string $title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the notification title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the notification content.
     *
     * @param $content string The content of the push notification
     *
     * @return PushNotification
     */
    public function setContent(string $content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get the notification content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
}