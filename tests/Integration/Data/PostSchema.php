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

/**
 * @package Neomerx\Tests\JsonApi
 */
class PostSchema extends DevSchemaProvider
{
    /**
     * @inheritdoc
     */
    protected $resourceType = 'posts';

    /**
     * @inheritdoc
     */
    protected $selfSubUrl = '/posts/';

    /**
     * @inheritdoc
     */
    public function getId($post)
    {
        return $post->{Post::ATTRIBUTE_ID};
    }

    /**
     * @inheritdoc
     */
    public function getAttributes($post)
    {
        assert('$post instanceof '.Post::class);

        return [
            Post::ATTRIBUTE_TITLE => $post->{Post::ATTRIBUTE_TITLE},
            Post::ATTRIBUTE_BODY  => $post->{Post::ATTRIBUTE_BODY},
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRelationships($post, array $includeRelationships = [])
    {
        assert('$post instanceof '.Post::class);

        $links = [];

        return $links;
    }
}
