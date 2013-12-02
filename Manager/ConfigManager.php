<?php

/*
 * This file is part of the ACLInterfaceBundle package.
 *
 * (c) Jérémy Hubert <jeremy.hubert@infogroom.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ifgm\ACLInterfaceBundle\Manager;

use Ifgm\ACLInterfaceBundle\Entity\EntityInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpKernel\Kernel;

class ConfigManager implements ConfigManagerInterface
{
    /**
     * Construct the manager
     *
     * @param Kernel $kernel
     * @param string $path
     * @param string $maskBuilder
     */
    public function __construct(Kernel $kernel, $path, $maskBuilder)
    {
        $this->maskBuilder = $maskBuilder;
        $path = $kernel->locateResource('@'.$path);
        $config = Yaml::parse($path);
        $this->config = $config['acl_config'];

        $replace = array();
        if (isset($config['parameters'])) {
            foreach ($config['parameters'] as $k => $v) {
                $replace['%' . $k . '%'] = $v;
            }

            $this->config = $this->replaceParameters($this->config, $replace);
        }

        return $this->config;
    }

    /**
     * Get config for namespace
     *
     * @param EntityInterface $entity
     *
     * @return bool
     */
    public function getConfig(EntityInterface $entity)
    {
        $namespace = get_class($entity);

        if (isset($this->config[$namespace])) {

            return $this->config[$namespace];
        }

        return false;
    }

    /**
     * Get form config for namespace
     *
     * @param EntityInterface $entity
     *
     * @return array|bool
     * @throws InvalidConfigurationException
     */
    public function getFormConfig(EntityInterface $entity)
    {
        $namespace = get_class($entity);

        if (isset($this->config[$namespace])) {

            $fields = array();
            foreach ($this->config[$namespace] as $field) {
                $maskName = array_keys($field);
                $maskName = reset($maskName);
                if (!defined($this->maskBuilder.'::MASK_'.$maskName)) {

                    throw new InvalidConfigurationException(sprintf('Mask "%s" is not defined in MaskBuilder'));
                }

                $fields[constant($this->maskBuilder.'::MASK_'.$maskName)] = reset($field);
            }

            return $fields;
        }

        return false;
    }

    /**
     * Replace parameters by their value
     *
     * @param array $arr
     * @param array $replace
     *
     * @return array
     */
    private function replaceParameters(array $arr, array $replace)
    {
        $rarr = array();
        foreach ($arr as $k => $v) {
            $rarr[str_replace(array_keys($replace), $replace, $k)] = is_array($v) ?
                    $this->replaceParameters($v, $replace) : str_replace(array_keys($replace), $replace, $v);
        }

        return $rarr;
    }
}