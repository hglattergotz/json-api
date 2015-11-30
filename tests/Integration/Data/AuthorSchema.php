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
class AuthorSchema extends DevSchemaProvider
{
    /**
     * @inheritdoc
     */
    protected $resourceType = 'people';

    /**
     * @inheritdoc
     */
    protected $selfSubUrl = '/people/';

    /**
     * @inheritdoc
     */
    public function getId($author)
    {
        return $author->{Author::ATTRIBUTE_ID};
    }

    /**
     * @inheritdoc
     */
    public function getAttributes($author)
    {
        return [
            Author::ATTRIBUTE_FIRST_NAME => $author->{Author::ATTRIBUTE_FIRST_NAME},
            Author::ATTRIBUTE_LAST_NAME  => $author->{Author::ATTRIBUTE_LAST_NAME},
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRelationships($author, array $includeRelationships = [])
    {
        assert('$author instanceof '.Author::class);

        $links = [
            Author::LINK_COMMENTS => [
                // closures for data are supported as well
                self::DATA => function () use ($author) {
                    return isset($author->{Author::LINK_COMMENTS}) ? $author->{Author::LINK_COMMENTS} : null;
                },
            ],
        ];

        // NOTE: The line(s) below for testing purposes only. Not for production.
        $this->fixLinks($links);

        return $links;
    }
}
