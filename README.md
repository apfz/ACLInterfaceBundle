IfgmACLInterfaceBundle
======================
The `IfgmACLInterfaceBundle` is an highly customizable bundle that provides an ACL Manager
and an interface which allows you to manage ACLs for an  array of users, on any entity.
This is an handy way to allow your users to manage access to their stuff in few steps.

You'll be able to manage ACLs from a simple form, and check access with the standard instruction
`$this->get('security.context')->isGranted('EDIT', $object)`

## Installation

### 1. Grab the bundle

Add on composer.json (see http://getcomposer.org/)

    "require" :  {
        // ...
        "ifgm/acli-interface-bundle":"dev-master",
    }

### 2. Register the bundle

To start using the bundle, register it in your Kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Ifgm\ACLInterfaceBundle\IfgmACLInterfaceBundle(),
    );
    // ...
}
```

### 3. Prepare some entities

You have to declare some entities to get ACLs working.

Your **User** Entity must implement `Ifgm\ACLInterfaceBundle\Model\UserInterface`
Entities which can have roles affected must implement `Ifgm\ACLInterfaceBundle\Model\EntityInterface`

Don't be scared, the only thing which is required to fit with these interfaces is a `getId()` function.

### 4. Configure the bundle

You need to add some configuration to get it working

```yaml
# app/config/config.yml
ifgm_acl_interface:
    user:
        class: Acme\DemoBundle\Entity\User
    acl:
        class: Acme\DemoBundle\Entity\Acl
```

You also need to declare which accesses can be given for your entities
Let's imagine you want to manage access to a private forum, you need first to define the accesses
which will be manageable. For this, you have to create a new configuration file in your bundle.

```yaml
# Acme/DemoBundle/Resource/config/acls.yml
acl_config:
    Acme\DemoBundle\Entity\Forum:
        - EDIT : Can edit forum details
        - CREATE : Can create sub-forums
        - DELETE : Can delete this forum
        - UNDELETE : Can undelete this forum
        - OWNER : Is owner of the forum
```

These ACLs (EDIT, CREATE...) are provided by the default MaskBuilder, the one used for the symfony2
embedded ACL system. You'll be told how to customize it at the last chapter.

And you need to declare a service to inject your new configuration

```yaml
# Acme/DemoBundle/Resource/config/services.yml
services:
    acme_demo.front_acls:
        class: %ifgm_acl_interface.config_manager.class%
        arguments:
            - @kernel
            - AcmeDemoBundle/Resources/config/acl_config.yml
            # Take care to the path syntax ! The same as Ressource location (@Acme...) but without the "@"
            - %ifgm_acl_interface.mask_builder.class%
        tags :
            - {name: ifgm_acl_interface.config_manager}
```

### Usage

You are now able to manage ACL through form with the following :

```php
    // Acme/DemoBundle/Controller/ForumController.php
    public function manageAclAction()
    {
        // Get an array of users, please note they have to be indexed by id
        $users = $this->getDoctrine()
            ->getRepository('AcmeDemoBundle:User')
            ->findAllIndexedById();

        $forum = $this->getDoctrine
            ->getRepository('AcmeDemoBundle:Forum')
            ->find(1);

        // This will create the form, process request if form is submitted, persist and flush updates
        $form = $this->get('ifgm.acl_manager')->manageForm($users, $forum);

        // Or you can disable flushing by setting third argument to false
        // $form = $this->get('ifgm.acl_manager')->manageForm($users, $forum, false);

        // You can display the forum with the default template, or just do what you want with $form
        // (which just need a $form->createView() to be displayed)
        return $this->get('ifgm.acl_manager')->renderForm($form);
    }
```

So, you got it, getting working ACLs form is nothing more than one line of code!
Please also note, you may want to use the following Trait to be able to use `$this->setIndexBy($qb, 'u.id')`
in your own UserRepository. It seems this have to be called after `->getQuery()`.
`Ifgm\ACLInterfaceBundle\Repository\Helper\IndexByTrait`

### Want some more?

Ok! Note that ACLManager has a lot of cool stuff you can use

```php
<?php
    $manager = $this->get('ifgm.acl_manager');

    $manager->addRole('EDIT', $user, $object)
    $manager->revokeRole('EDIT', $user, $object)
    $manager->setRoles(array('EDIT'), $user, $object) // Will replace current user's roles by the ones provided in the array
    $manager->setBitmask(28) // Adds roles by using bitmask, use with care
    $manager->revokeAll($user, $object) // Revokes all roles of a user on target object
    $manager->revokeAllRolesFromUser($user) // Delete all ACLs entries for this user (e.g. for user deletion)
    $manager->revokeAllRolesOnObject($object) // The same for object deletion
    $manager->getUsersRoles($user, $object) // Get an array of roles array(in bitmask => 'role label', ...)
    $manager->getAllRolesOnObject($object) // Get an array of roles for each user
    $manager->manageForm($user, $object, $flush)
    $manager->renderForm($form) // Returns a Response object
```

### Customize MaskBuilder

Here you'll find the full configuration options, and default values

```yaml
        ifgm_acl_interface:
            form_manager:
                class: Ifgm\ACLInterfaceBundle\Manager\FormManager
        mask_builder:
            class: Ifgm\ACLInterfaceBundle\Security\Acl\Permission\MaskBuilder
            # Please note this MaskBuilder refers to the default one provided with Symfony2, just flavoured with some custom functions
        permission_map:
            class: Symfony\Component\Security\Acl\Permission\BasicPermissionMap
        acl_voter:
            class: Ifgm\ACLInterfaceBundle\Security\Authorization\Voter\AclVoter
        acl:
            class: Ifgm\ACLInterfaceBundle\Entity\Acl
        user:
            class: # Must be set
```

You can define your own MaskBuilder, just be sure it implement the following interface :
`Ifgm\ACLInterfaceBundle\Security\Acl\Permission\MaskBuilderInterface`

You may want to use following trait to be sure having bundle's required methods :
`Ifgm\ACLInterfaceBundle\Security\Acl\Permission\MaskBuilderTrait`