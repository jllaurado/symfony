<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\FrameworkBundle\Tests\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class PhpFrameworkExtensionTest extends FrameworkExtensionTest
{
    protected function loadFromFile(ContainerBuilder $container, $file)
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/Fixtures/php'));
        $loader->load($file.'.php');
    }

    /**
     * @expectedException \LogicException
     */
    public function testAssetsCannotHavePathAndUrl()
    {
        $this->createContainerFromClosure(function ($container) {
            $container->loadFromExtension('framework', [
                'assets' => [
                    'base_urls' => 'http://cdn.example.com',
                    'base_path' => '/foo',
                ],
            ]);
        });
    }

    /**
     * @expectedException \LogicException
     */
    public function testAssetPackageCannotHavePathAndUrl()
    {
        $this->createContainerFromClosure(function ($container) {
            $container->loadFromExtension('framework', [
                'assets' => [
                    'packages' => [
                        'impossible' => [
                            'base_urls' => 'http://cdn.example.com',
                            'base_path' => '/foo',
                        ],
                    ],
                ],
            ]);
        });
    }

    /**
     * @expectedException \Symfony\Component\Workflow\Exception\InvalidDefinitionException
     * @expectedExceptionMessage A transition from a place/state must have an unique name. Multiple transitions named "a_to_b" from place/state "a" where found on StateMachine "article".
     */
    public function testWorkflowValidationStateMachine()
    {
        $this->createContainerFromClosure(function ($container) {
            $container->loadFromExtension('framework', [
                'workflows' => [
                    'article' => [
                        'type' => 'state_machine',
                        'supports' => [
                            __CLASS__,
                        ],
                        'places' => [
                            'a',
                            'b',
                            'c',
                        ],
                        'transitions' => [
                            'a_to_b' => [
                                'from' => ['a'],
                                'to' => ['b', 'c'],
                            ],
                        ],
                    ],
                ],
            ]);
        });
    }

    /**
     * @expectedException \Symfony\Component\Workflow\Exception\InvalidDefinitionException
     * @expectedExceptionMessage The marking store of workflow "article" can not store many places. But the transition "a_to_b" has too many output (2). Only one is accepted.
     */
    public function testWorkflowValidationMethodSingle()
    {
        $this->createContainerFromClosure(function ($container) {
            $container->loadFromExtension('framework', [
                'workflows' => [
                    'article' => [
                        'type' => 'workflow',
                        'marking_store' => [
                            'type' => 'method',
                            'arguments' => [
                                true,
                            ],
                        ],
                        'supports' => [
                            __CLASS__,
                        ],
                        'places' => [
                            'a',
                            'b',
                            'c',
                        ],
                        'transitions' => [
                            'a_to_b' => [
                                'from' => ['a'],
                                'to' => ['b', 'c'],
                            ],
                        ],
                    ],
                ],
            ]);
        });
    }

    public function testWorkflowValidationMethodNotSingle()
    {
        $this->createContainerFromClosure(function ($container) {
            $container->loadFromExtension('framework', [
                'workflows' => [
                    'article' => [
                        'type' => 'workflow',
                        'marking_store' => [
                            'type' => 'method',
                            'arguments' => [
                                false,
                            ],
                        ],
                        'supports' => [
                            __CLASS__,
                        ],
                        'places' => [
                            'a',
                            'b',
                            'c',
                        ],
                        'transitions' => [
                            'a_to_b' => [
                                'from' => ['a'],
                                'to' => ['b', 'c'],
                            ],
                        ],
                    ],
                ],
            ]);
        });

        // the test ensures that the validation does not fail (i.e. it does not throw any exceptions)
        $this->addToAssertionCount(1);
    }

    public function testWorkflowValidationMultipleState()
    {
        $this->createContainerFromClosure(function ($container) {
            $container->loadFromExtension('framework', [
                'workflows' => [
                    'article' => [
                        'type' => 'workflow',
                        'marking_store' => [
                            'type' => 'multiple_state',
                        ],
                        'supports' => [
                            __CLASS__,
                        ],
                        'places' => [
                            'a',
                            'b',
                            'c',
                        ],
                        'transitions' => [
                            'a_to_b' => [
                                'from' => ['a'],
                                'to' => ['b', 'c'],
                            ],
                        ],
                    ],
                ],
            ]);
        });

        // the test ensures that the validation does not fail (i.e. it does not throw any exceptions)
        $this->addToAssertionCount(1);
    }

    /**
     * @expectedException \Symfony\Component\Workflow\Exception\InvalidDefinitionException
     * @expectedExceptionMessage The marking store of workflow "article" can not store many places. But the transition "a_to_b" has too many output (2). Only one is accepted.
     */
    public function testWorkflowValidationSingleState()
    {
        $this->createContainerFromClosure(function ($container) {
            $container->loadFromExtension('framework', [
                'workflows' => [
                    'article' => [
                        'type' => 'workflow',
                        'marking_store' => [
                            'type' => 'single_state',
                        ],
                        'supports' => [
                            __CLASS__,
                        ],
                        'places' => [
                            'a',
                            'b',
                            'c',
                        ],
                        'transitions' => [
                            'a_to_b' => [
                                'from' => ['a'],
                                'to' => ['b', 'c'],
                            ],
                        ],
                    ],
                ],
            ]);
        });

        // the test ensures that the validation does not fail (i.e. it does not throw any exceptions)
        $this->addToAssertionCount(1);
    }
}
