<?php namespace Neomerx\Tests\JsonApi\Integration\Data;

/**
 * Copyright 2015 info@neomerx.com (www.neomerx.com)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

use \Neomerx\JsonApi\Contracts\Schema\ContainerInterface;
use \Neomerx\JsonApi\Contracts\Schema\SchemaFactoryInterface;

/**
 * @package Neomerx\Tests\JsonApi
 */
class SiteSchema extends DevSchemaProvider
{
    /**
     * @inheritdoc
     */
    protected $resourceType = 'sites';

    /**
     * @inheritdoc
     */
    protected $selfSubUrl = '/sites/';

    /**
     * @param SchemaFactoryInterface $factory
     * @param ContainerInterface     $container
     */
    public function __construct(SchemaFactoryInterface $factory, ContainerInterface $container)
    {
        parent::__construct($factory, $container);
    }

    /**
     * @inheritdoc
     */
    public function getId($site)
    {
        return $site->{Site::ATTRIBUTE_ID};
    }

    /**
     * @inheritdoc
     */
    public function getAttributes($site)
    {
        assert('$site instanceof '.Site::class);

        return [
            Site::ATTRIBUTE_NAME => $site->{Site::ATTRIBUTE_NAME},
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRelationships($site, array $includeRelationships = [])
    {
        assert('$site instanceof '.Site::class);

        $relationships = [];

        if (in_array('posts', $includeRelationships)) {
            $relationships[Site::LINK_POSTS] = [
                self::DATA  => $site->{Site::LINK_POSTS}
            ];
        }

        return $relationships;
    }
}
