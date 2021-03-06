<?php

namespace Asanpay\Shaparak;

use Asanpay\Shaparak\Provider\AsanPardakhtProvider;
use Asanpay\Shaparak\Provider\MellatProvider;
use Asanpay\Shaparak\Provider\MelliProvider;
use Asanpay\Shaparak\Provider\PasargadProvider;
use Asanpay\Shaparak\Provider\SaderatProvider;
use Illuminate\Support\Arr;
use Illuminate\Support\Manager;
use InvalidArgumentException;
use Asanpay\Shaparak\Contracts\Transaction;
use Asanpay\Shaparak\Contracts\Provider;
use Asanpay\Shaparak\Provider\SamanProvider;
use Asanpay\Shaparak\Provider\ParsianProvider;

class ShaparakManager extends Manager implements Contracts\Factory
{
    /**
     * runtime driver configuration
     *
     * @var array
     */
    protected array $runtimeConfig;

    /**
     * transaction which should paid on the gateway
     *
     * @var Transaction $transaction
     */
    protected Transaction $transaction;

    /**
     * Get a driver instance.
     *
     * @param string $driver driver name
     * @param Transaction $transaction
     * @param array $config  runtime configuration for the driver instead of reading from config file
     *
     * @return mixed
     */
    public function with(string $driver, Transaction $transaction, array $config = [])
    {
        $this->transaction = $transaction;

        if (!empty($config)) {
            $this->runtimeConfig = $config;
        }

        return $this->driver($driver);
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return Provider
     */
    protected function createSamanDriver()
    {
        $config = $this->getConfig('saman');

        return $this->buildProvider(
            SamanProvider::class,
            $config
        );
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return Provider
     */
    protected function createParsianDriver()
    {
        $config = $this->getConfig('parsian');

        return $this->buildProvider(
            ParsianProvider::class,
            $config
        );
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return Provider
     */
    protected function createPasargadDriver()
    {
        $config = $this->getConfig('pasargad');

        return $this->buildProvider(
            PasargadProvider::class,
            $config
        );
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return Provider
     */
    protected function createMellatDriver()
    {
        $config = $this->getConfig('mellat');

        return $this->buildProvider(
            MellatProvider::class,
            $config
        );
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return Provider
     */
    protected function createMelliDriver()
    {
        $config = $this->getConfig('melli');

        return $this->buildProvider(
            MelliProvider::class,
            $config
        );
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return Provider
     */
    protected function createSaderatDriver()
    {
        $config = $this->getConfig('saderat');

        return $this->buildProvider(
            SaderatProvider::class,
            $config
        );
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return Provider
     */
    protected function createAsanPardakhtDriver()
    {
        $config = $this->getConfig('adanardakht');

        return $this->buildProvider(
            AsanPardakhtProvider::class,
            $config
        );
    }

    /**
     * Build a Shaparak provider instance.
     *
     * @param string $provider
     *
     * @param array $config
     *
     * @return Provider
     */
    public function buildProvider($provider, array $config): Provider
    {
        return new $provider(
            $this->transaction,
            $config,
            Arr::get($config, 'mode', config('shaparak.mode', 'production')),
            Arr::get($config, 'httpClientOptions', [])
        );
    }

    /**
     * Get the default driver name.
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function getDefaultDriver()
    {
        throw new InvalidArgumentException('No Shaparak driver was specified.');
    }

    /**
     * get provider configuration runtime array or config based configuration
     *
     * @param string $driver
     *
     * @return array
     */
    protected function getConfig(string $driver): array
    {
        if (empty($this->runtimeConfig)) {
            return $this->container['config']["shaparak.providers.{$driver}"];
        }

        return $this->runtimeConfig;
    }

    /**
     * @param string $message
     * @param array $params
     * @param string $level
     */
    public static function log(string $message, array $params = [], string $level = 'debug'): void
    {
        $message = "SHAPARAK -> " . $message;

        forward_static_call(['Tartan\Log\Facades\XLog', $level], $message, $params);
    }
}
