<?php

namespace Mundipagg\Core\Webhook\Aggregates;

use Mundipagg\Core\Kernel\Abstractions\AbstractEntity;
use Mundipagg\Core\Webhook\ValueObjects\WebhookType;

class Webhook extends AbstractEntity
{
    /**
     *
     * @var WebhookType 
     */
    protected $type;

    /**
     *
     * @var AbstractEntity 
     */
    protected $entity;

    /**
     *
     * @return WebhookType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     *
     * @param  WebhookType $type
     * @return Webhook
     */
    public function setType(WebhookType $type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     *
     * @return AbstractEntity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     *
     * @param  AbstractEntity $entity
     * @return Webhook
     */
    public function setEntity(AbstractEntity $entity)
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @link   https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since  5.4.0
     */
    public function jsonSerialize()
    {
        // TODO: Implement jsonSerialize() method.
    }
}