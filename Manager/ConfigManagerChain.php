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

class ConfigManagerChain implements ConfigManagerInterface
{
    /**
     * @var array<ConfigManagerInterface>
     */
    protected $configManagers;

    /**
     * @param array<ConfigManagerInterface> $configManagers
     */
    public function __construct(array $configManagers)
    {
        $this->configManagers = $configManagers;
    }

    /**
     * Add ConfigManager
     *
     * @param ConfigManagerInterface $configManager
     */
    public function addConfigManager(ConfigManagerInterface $configManager)
    {
        $this->configManagers[] = $configManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig($entity)
    {
        foreach ($this->configManagers as $configManager) {
            $result = $configManager->getConfig($entity);
            if ($result) {

                return $result;
            }
        }

        return array('ACL_MANAGE' => 'Manage ACLs');
    }

    /**
     * {@inheritdoc}
     */
    public function getFormConfig($entity)
    {
        foreach ($this->configManagers as $configManager) {
            $result = $configManager->getFormConfig($entity);
            if ($result) {

                return $result;
            }
        }

        return array('ACL_MANAGE' => 'Manage ACLs');
    }
}