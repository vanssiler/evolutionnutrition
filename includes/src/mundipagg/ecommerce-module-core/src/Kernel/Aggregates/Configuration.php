<?php

namespace Mundipagg\Core\Kernel\Aggregates;

use Exception;
use Mundipagg\Core\Kernel\Abstractions\AbstractEntity;
use Mundipagg\Core\Kernel\Exceptions\InvalidParamException;
use Mundipagg\Core\Kernel\ValueObjects\AbstractValidString;
use Mundipagg\Core\Kernel\ValueObjects\Configuration\AddressAttributes;
use Mundipagg\Core\Kernel\ValueObjects\Configuration\CardConfig;
use Mundipagg\Core\Kernel\ValueObjects\Configuration\RecurrenceConfig;
use Mundipagg\Core\Kernel\ValueObjects\Key\AbstractSecretKey;
use Mundipagg\Core\Kernel\ValueObjects\Key\AbstractPublicKey;
use Mundipagg\Core\Kernel\ValueObjects\Key\TestPublicKey;
use Mundipagg\Core\Kernel\ValueObjects\Id\GUID;

final class Configuration extends AbstractEntity
{
    const KEY_SECRET = 'KEY_SECRET';
    const KEY_PUBLIC = 'KEY_PUBLIC';

    const CARD_OPERATION_AUTH_ONLY = 'auth_only';
    const CARD_OPERATION_AUTH_AND_CAPTURE = 'auth_and_capture';

    /**
     *
     * @var bool 
     */
    private $enabled;
    /**
     *
     * @var bool 
     */
    private $boletoEnabled;
    /**
     *
     * @var bool 
     */
    private $creditCardEnabled;
    /**
     *
     * @var bool 
     */
    private $twoCreditCardsEnabled;
    /**
     *
     * @var bool 
     */
    private $boletoCreditCardEnabled;
    /**
     *
     * @var bool 
     */
    private $testMode;
    /**
     *
     * @var GUID 
     */
    private $hubInstallId;

    /** @var string */
    private $cardOperation;

    /**
     *
     * @var AbstractValidString[]
     */
    private $keys;

    /**
     *
     * @var CardConfig[]
     */
    private $cardConfigs;


    /**
     * @var bool
     */
    private $antifraudEnabled;

    /**
     * @var int
     */
    private $antifraudMinAmount;

    /** @var bool */
    private $installmentsEnabled;

    /** @var AddressAttributes */
    private $addressAttributes;

    /** @var string */
    private $cardStatementDescriptor;

    /** @var string */
    private $boletoInstructions;


    /** @var string */
    private $storeId;

    /** @var Configuration */
    private $parentConfiguration;

    /** @var array */
    private $methodsInherited;

    /** @var bool */
    private $inheritAll;

    /** @var bool */
    private $saveCards;

    /** @var bool */
    private $multiBuyer;

    /** @var RecurrenceConfig */
    private $recurrenceConfig;

    /** @var bool */
    private $installmentsDefaultConfig;

    public function __construct()
    {
        $this->saveCards = false;
        $this->multiBuyer = false;
        $this->cardConfigs = [];
        $this->methodsInherited = [];

        $this->keys = [
            self::KEY_SECRET => null,
            self::KEY_PUBLIC => null,
        ];

        $this->testMode = true;
        $this->inheritAll = false;
        $this->installmentsDefaultConfig = false;
    }

    /**
     * @return RecurrenceConfig
     */
    public function getRecurrenceConfig()
    {
        return $this->recurrenceConfig;
    }

    /**
     * @param RecurrenceConfig $recurrenceConfig
     */
    public function setRecurrenceConfig(RecurrenceConfig $recurrenceConfig)
    {
        $this->recurrenceConfig = $recurrenceConfig;
    }

    protected function isEnabled()
    {
        return $this->enabled;
    }

    public function setEnabled($enabled)
    {
        $this->enabled = filter_var(
            $enabled,
            FILTER_VALIDATE_BOOLEAN
        );
    }

    protected function getPublicKey()
    {
        return $this->keys[self::KEY_PUBLIC];
    }

    protected function getSecretKey()
    {
        return $this->keys[self::KEY_SECRET];
    }

    /**
     *
     * @param  string|array $key
     * @return $this
     */
    public function setPublicKey(AbstractPublicKey $key)
    {
        $this->testMode = false;

        $this->keys[self::KEY_PUBLIC] = $key;

        if (is_a($key, TestPublicKey::class)) {
            $this->testMode = true;
        };

        return $this;
    }

    /**
     *
     * @param  string|array $key
     * @return $this
     */
    public function setSecretKey(AbstractSecretKey $key)
    {
        $this->keys[self::KEY_SECRET] = $key;
        return $this;
    }

    /**
     *
     * @return bool
     */
    protected function isTestMode()
    {
        return $this->testMode;
    }

    /**
     *
     * @return bool
     */
    protected function isHubEnabled()
    {
        if ($this->hubInstallId === null) {
            return false;
        }
        return $this->hubInstallId->getValue() !== null;
    }

    public function setHubInstallId(GUID $hubInstallId)
    {
        $this->hubInstallId = $hubInstallId;
    }

    protected function getHubInstallId()
    {
        return $this->hubInstallId;
    }

    /**
     *
     * @param  bool $boletoEnabled
     * @return Configuration
     */
    public function setBoletoEnabled($boletoEnabled)
    {
        $this->boletoEnabled = filter_var(
            $boletoEnabled,
            FILTER_VALIDATE_BOOLEAN
        );
        return $this;
    }

    /**
     *
     * @param  bool $creditCardEnabled
     * @return Configuration
     */
    public function setCreditCardEnabled($creditCardEnabled)
    {
        $this->creditCardEnabled = filter_var(
            $creditCardEnabled,
            FILTER_VALIDATE_BOOLEAN
        );
        return $this;
    }

    /**
     *
     * @param  bool $twoCreditCardsEnabled
     * @return Configuration
     */
    public function setTwoCreditCardsEnabled($twoCreditCardsEnabled)
    {
        $this->twoCreditCardsEnabled = filter_var(
            $twoCreditCardsEnabled,
            FILTER_VALIDATE_BOOLEAN
        );
        return $this;
    }

    /**
     *
     * @param  bool $boletoCreditCardEnabled
     * @return Configuration
     */
    public function setBoletoCreditCardEnabled($boletoCreditCardEnabled)
    {
        $this->boletoCreditCardEnabled = filter_var(
            $boletoCreditCardEnabled,
            FILTER_VALIDATE_BOOLEAN
        );
        return $this;
    }

    /**
     *
     * @return bool
     */
    protected function isBoletoEnabled()
    {
        return $this->boletoEnabled;
    }

    /**
     *
     * @return bool
     */
    protected function isCreditCardEnabled()
    {
        return $this->creditCardEnabled;
    }

    /**
     *
     * @return bool
     */
    protected function isTwoCreditCardsEnabled()
    {
        return $this->twoCreditCardsEnabled;
    }

    /**
     *
     * @return bool
     */
    protected function isBoletoCreditCardEnabled()
    {
        return $this->boletoCreditCardEnabled;
    }

    /**
     *
     * @param  CardConfig $installmentConfig
     * @throws Exception
     */
    public function addCardConfig(CardConfig $newCardConfig)
    {
        $cardConfigs = $this->getCardConfigs();
        foreach ($cardConfigs as $cardConfig) {
            if ($cardConfig->equals($newCardConfig)) {
                throw new InvalidParamException(
                    "The card config is already added!",
                    $newCardConfig->getBrand()
                );
            }
        }

        $this->cardConfigs[] = $newCardConfig;
    }

    /**
     *
     * @return CardConfig[]
     */
    protected function getCardConfigs()
    {
        return $this->cardConfigs !== null ? $this->cardConfigs : [];
    }

    /**
     * @return string
     */
    protected function getCardOperation()
    {
        return $this->cardOperation;
    }

    /**
     * @param string $cardOperation
     */
    public function setCardOperation($cardOperation)
    {
        $this->cardOperation = $cardOperation;
    }

    /**
     * @return bool
     */
    protected function isCapture()
    {
        return $this->getCardOperation() === self::CARD_OPERATION_AUTH_AND_CAPTURE;
    }

    /**
     * @return bool
     */
    protected function isAntifraudEnabled()
    {
        return $this->antifraudEnabled;
    }

    /**
     * @param bool $antifraudEnabled
     */
    public function setAntifraudEnabled($antifraudEnabled)
    {
        $this->antifraudEnabled = $antifraudEnabled;
    }

    /**
     * @return int
     */
    protected function getAntifraudMinAmount()
    {
        return $this->antifraudMinAmount;
    }

    /**
     * @param int $antifraudMinAmount
     * @throws InvalidParamException
     */
    public function setAntifraudMinAmount($antifraudMinAmount)
    {
        $numbers = '/([^0-9])/i';
        $replace = '';

        $minAmount = preg_replace($numbers, $replace, $antifraudMinAmount);

        if ($minAmount < 0) {
            throw new InvalidParamException(
                'AntifraudMinAmount should be at least 0!',
                $minAmount
            );
        }
        $this->antifraudMinAmount = $minAmount;
    }

    /**
     * @return bool
     */
    protected function isInstallmentsEnabled()
    {
        return $this->installmentsEnabled;
    }

    /**
     * @param bool $installmentsEnabled
     */
    public function setInstallmentsEnabled($installmentsEnabled)
    {
        $this->installmentsEnabled = $installmentsEnabled;
    }

    /**
     * @return AddressAttributes
     */
    protected function getAddressAttributes()
    {
        return $this->addressAttributes;
    }

    /**
     * @param AddressAttributes $addressAttributes
     */
    public function setAddressAttributes(AddressAttributes $addressAttributes)
    {
        $this->addressAttributes = $addressAttributes;
    }

    /**
     * @return string
     */
    protected function getCardStatementDescriptor()
    {
        return $this->cardStatementDescriptor;
    }

    /**
     * @param string $cardStatementDescriptor
     */
    public function setCardStatementDescriptor($cardStatementDescriptor)
    {
        $this->cardStatementDescriptor = $cardStatementDescriptor;
    }

    /**
     * @return string
     */
    protected function getBoletoInstructions()
    {
        return $this->boletoInstructions;
    }

    /**
     * @param string $boletoInstructions
     */
    public function setBoletoInstructions($boletoInstructions)
    {
        $this->boletoInstructions = $boletoInstructions;
    }

    /**
     * @return bool
     */
    public function isSaveCards()
    {
        return $this->saveCards;
    }

    /**
     * @param bool $saveCards
     */
    public function setSaveCards($saveCards)
    {
        $this->saveCards = $saveCards;
    }

    /**
     * @return bool
     */
    public function isMultiBuyer()
    {
        return $this->multiBuyer;
    }

    /**
     * @param bool $multiBuyer
     */
    public function setMultiBuyer($multiBuyer)
    {
        $this->multiBuyer = $multiBuyer;
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
        return [
            "enabled" => $this->enabled,
            "antifraudEnabled" => $this->isAntifraudEnabled(),
            "antifraudMinAmount" => $this->getAntifraudMinAmount(),
            "boletoEnabled" => $this->boletoEnabled,
            "creditCardEnabled" => $this->creditCardEnabled,
            "saveCards" => $this->isSaveCards(),
            "multiBuyer" => $this->isMultiBuyer(),
            "twoCreditCardsEnabled" => $this->twoCreditCardsEnabled,
            "boletoCreditCardEnabled" => $this->boletoCreditCardEnabled,
            "testMode" => $this->testMode,
            "hubInstallId" => $this->isHubEnabled() ? $this->hubInstallId->getValue() : null,
            "addressAttributes" => $this->getAddressAttributes(),
            "keys" => $this->keys,
            "cardOperation" => $this->cardOperation,
            "installmentsEnabled" => $this->isInstallmentsEnabled(),
            "installmentsDefaultConfig" => $this->isInstallmentsDefaultConfig(),
            "cardStatementDescriptor" => $this->getCardStatementDescriptor(),
            "boletoInstructions" => $this->getBoletoInstructions(),
            "cardConfigs" => $this->getCardConfigs(),
            "storeId" => $this->getStoreId(),
            "methodsInherited" => $this->getMethodsInherited(),
            "parentId" => $this->getParentId(),
            "parent" => $this->parentConfiguration,
            "inheritAll" => $this->isInheritedAll(),
            "recurrenceConfig" => $this->getRecurrenceConfig()
        ];
    }

    /**
     * @return string
     */
    public function getStoreId()
    {
        return $this->storeId;
    }

    /**
     * @param string $storeId
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
    }

    /**
     * @param Configuration $parentConfiguration
     */
    public function setParentConfiguration(Configuration $parentConfiguration)
    {
        $this->parentConfiguration = $parentConfiguration;
    }

    /**
     * @return int
     */
    public function getParentId()
    {
        if ($this->parentConfiguration === null) {
            return null;
        }
        return $this->parentConfiguration->getId();
    }

    /**
     * @param array $methods
     */
    public function setMethodsInherited($methods)
    {
        $this->methodsInherited = $methods;
    }

    /**
     * @return array
     */
    public function getMethodsInherited()
    {
        if ($this->parentConfiguration === null) {
            return [];
        }
        return $this->methodsInherited;
    }

    /**
     * @return bool
     */
    public function isInheritedAll()
    {
        if ($this->parentConfiguration === null) {
            return false;
        }

        return $this->inheritAll;
    }

    /**
     * @param bool $inheritAll
     */
    public function setInheritAll($inheritAll)
    {
        $this->inheritAll = $inheritAll;
    }

    /**
     * @return bool
     */
    public function isInstallmentsDefaultConfig()
    {
        return $this->installmentsDefaultConfig;
    }

    /**
     * @param bool $installmentsDefaultConfig
     * @return Configuration
     */
    public function setInstallmentsDefaultConfig($installmentsDefaultConfig)
    {
        $this->installmentsDefaultConfig = $installmentsDefaultConfig;
        return $this;
    }

    public function __call($method, $arguments)
    {
        $methodSplited = explode(
            "_",
            preg_replace('/(?<=\\w)(?=[A-Z])/',"_$1", $method)
        );

        $targetObject = $this;

        $actions = ['is', 'get'];
        $useDefault = in_array($method, $this->getMethodsInherited());

        if ((in_array($methodSplited[0], $actions) && $useDefault) || $this->isInheritedAll()) {
            if ($this->parentConfiguration !== null) {
                $targetObject = $this->parentConfiguration;
            }
        }

        return call_user_func([$targetObject, $method], $arguments);
    }
}