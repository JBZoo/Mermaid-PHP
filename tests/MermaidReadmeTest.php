<?php

/**
 * JBZoo Toolbox - Mermaid-PHP
 *
 * This file is part of the JBZoo Toolbox project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Mermaid-PHP
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/Mermaid-PHP
 */

declare(strict_types=1);

namespace JBZoo\PHPUnit;

/**
 * Class MermaidPhpReadmeTest
 *
 * @package JBZoo\PHPUnit
 */
class MermaidPhpReadmeTest extends AbstractReadmeTest
{
    protected $packageName = 'Mermaid-PHP';

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->params['scrutinizer'] = true;
        $this->params['codefactor'] = true;
        $this->params['strict_types'] = true;
    }

    /**
     * @return string|null
     */
    protected function checkBadgeTravis(): ?string
    {
        return $this->getPreparedBadge($this->getBadge(
            'Build Status',
            'https://travis-ci.org/__VENDOR_ORIG__/__PACKAGE_ORIG__.svg?branch=master',
            'https://travis-ci.org/__VENDOR_ORIG__/__PACKAGE_ORIG__'
        ));
    }
}
