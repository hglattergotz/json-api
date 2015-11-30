<?php namespace Neomerx\Tests\JsonApi\Sample;

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

use \Closure;
use \Mockery;
use \Neomerx\JsonApi\Schema\Link;
use \Neomerx\JsonApi\Factories\Factory;
use \Neomerx\JsonApi\Encoder\Encoder;
use \Neomerx\JsonApi\Encoder\EncoderOptions;
use \Neomerx\JsonApi\Parameters\EncodingParameters;
use \Neomerx\JsonApi\Contracts\Integration\CurrentRequestInterface;
use \Neomerx\JsonApi\Contracts\Parameters\Headers\MediaTypeInterface;
use \Neomerx\JsonApi\Contracts\Parameters\ParametersParserInterface;
use \Neomerx\JsonApi\Contracts\Integration\ExceptionThrowerInterface;
use \Neomerx\Tests\JsonApi\Integration\Data\Post;
use \Neomerx\Tests\JsonApi\Integration\Data\Site;
use \Neomerx\Tests\JsonApi\Integration\Data\SiteSchema;
use \Neomerx\Tests\JsonApi\Integration\Data\Posts;
use \Neomerx\Tests\JsonApi\Integration\Data\PostSchema;
use \Neomerx\Tests\JsonApi\Integration\Data\Author;
use \Neomerx\Tests\JsonApi\Integration\Data\AuthorSchema;
use \Neomerx\Tests\JsonApi\Integration\Data\Comment;
use \Neomerx\Tests\JsonApi\Integration\Data\CommentSchema;
use \Neomerx\Tests\JsonApi\BaseTestCase;

/**
 * @package Neomerx\Tests\JsonApi
 */
class DefaultsTest extends BaseTestCase
{
    /** JSON API type */
    const TYPE = MediaTypeInterface::JSON_API_MEDIA_TYPE;

    /**
     * @var ParametersParserInterface
     */
    private $parser;

    /**
     * @var MockInterface
     */
    private $mockRequest;

    /**
     * @var MockInterface
     */
    private $mockThrower;

    /**
     * @var Encoder
     */
    private $encoder;

    /**
     * Set up.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->parser      = (new Factory())->createParametersParser();
        $this->mockRequest = Mockery::mock(CurrentRequestInterface::class);
        $this->mockThrower = Mockery::mock(ExceptionThrowerInterface::class);
        $this->encoder     = Encoder::instance([
            Author::class  => AuthorSchema::class,
            Comment::class => CommentSchema::class,
            Post::class    => PostSchema::class,
            Site::class    => SiteSchema::class
        ], new EncoderOptions(JSON_PRETTY_PRINT));
    }

    /**
     * The most basic test case, for GET /sites/1
     *
     * NOTE: The integration tests have their own models and schemas that do NOT
     *       include any relationships by default. This is different from the
     *       schemas found in sample/Models and sample/Schema. This is to simplify
     *       things
     */
    public function testWithoutParameters()
    {
        $parameters = $this->parser->parse(
            $this->prepareRequest(self::TYPE, self::TYPE, []),
            $this->prepareExceptions()
        );

        $actual = $this->encoder->encodeData($this->getFullDataSet(), $parameters);
        $expected = <<<EOL
        {
            "data": {
                "type": "sites",
                "id": "1",
                "attributes": {
                    "name": "JSON API Samples"
                },
                "links": {
                    "self": "/sites/1"
                }
            }
        }
EOL;

        $this->assertEquals($this->normalize($expected), $this->normalize($actual));
    }

    /**
     * testIncludePostsOnly
     *
     * @access public
     * @return void
     */
    public function testIncludePostsOnly()
    {
        $input = [
            'include' => 'posts'
        ];
        $parameters = $this->parser->parse(
            $this->prepareRequest(self::TYPE, self::TYPE, $input),
            $this->prepareExceptions()
        );

        $this->assertEquals(['posts'], $parameters->getIncludePaths());
        $this->assertEquals(null, $parameters->getFieldSets());
        $actual = $this->encoder->encodeData($this->getFullDataSet(), $parameters);
        $expected = <<<EOL
        {
            "data": {
                "type": "sites",
                "id": "1",
                "attributes": {
                    "name": "JSON API Samples"
                },
                "relationships": {
                    "posts": {
                        "data": [
                            {
                                "type": "posts",
                                "id": "321"
                            }
                        ]
                    }
                },
                "links": {
                    "self": "/sites/1"
                }
            },
            "included": [
                {
                    "type": "posts",
                    "id": "321",
                    "attributes": {
                        "title": "Included objects",
                        "body": "Yes, it is supported"
                    }
                }
            ]
        }
EOL;

        $this->assertEquals($this->normalize($expected), $this->normalize($actual));
    }

    /**
     * testIncludePostsOnlyWithFieldSet
     *
     * @access public
     * @return void
     */
    public function testIncludePostsOnlyWithFieldset()
    {
        $input = [
            'include' => 'posts',
            'fields'  => ['posts' => 'title']
        ];

        // This test only passes if the fields parameters includes the following
        // It should work without this!
        //$input['fields']['sites'] = 'name,posts';
        $parameters = $this->parser->parse(
            $this->prepareRequest(self::TYPE, self::TYPE, $input),
            $this->prepareExceptions()
        );

        $this->assertEquals(['posts'], $parameters->getIncludePaths());
        //$this->assertEquals(['posts' => ['title']], $parameters->getFieldSets());
        $actual = $this->encoder->encodeData($this->getFullDataSet(), $parameters);
        $expected = <<<EOL
        {
            "data": {
                "type": "sites",
                "id": "1",
                "attributes": {
                    "name": "JSON API Samples"
                },
                "relationships": {
                    "posts": {
                        "data": [
                            {
                                "type": "posts",
                                "id": "321"
                            }
                        ]
                    }
                },
                "links": {
                    "self": "/sites/1"
                }
            },
            "included": [
                {
                    "type": "posts",
                    "id": "321",
                    "attributes": {
                        "title": "Included objects"
                    }
                }
            ]
        }
EOL;

        $this->assertEquals($this->normalize($expected), $this->normalize($actual));
    }
    /**
     * @return \Neomerx\Tests\JsonApi\Integration\Data\Site
     */
    private function getFullDataset()
    {
        $author   = Author::instance('123', 'John', 'Dow');
        $comments = [
            Comment::instance('456', 'Included objects work as easy as basic ones', $author),
            Comment::instance('789', 'Let\'s try!', $author),
        ];
        $post = Post::instance('321', 'Included objects', 'Yes, it is supported', $author, $comments);
        $site = Site::instance('1', 'JSON API Samples', [$post]);

        return $site;
    }

    /**
     * @param string $json
     *
     * @return string
     */
    private function normalize($json)
    {
        return json_encode(json_decode($json));
    }

    /**
     * @param string $contentType
     * @param string $accept
     * @param array  $input
     * @param int    $contentTypeTimes
     * @param int    $acceptTimes
     * @param int    $parametersTimes
     *
     * @return CurrentRequestInterface
     */
    private function prepareRequest(
        $contentType,
        $accept,
        array $input,
        $contentTypeTimes = 1,
        $acceptTimes = 1,
        $parametersTimes = 1
    ) {
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $this->mockRequest->shouldReceive('getHeader')->with('Content-Type')
            ->times($contentTypeTimes)->andReturn($contentType);
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $this->mockRequest->shouldReceive('getHeader')->with('Accept')->times($acceptTimes)->andReturn($accept);
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $this->mockRequest->shouldReceive('getQueryParameters')
            ->withNoArgs()->times($parametersTimes)->andReturn($input);

        /** @var CurrentRequestInterface $request */
        $request = $this->mockRequest;

        return $request;
    }

    /**
     * @param string $exceptionMethod
     * @param int    $times
     *
     * @return ExceptionThrowerInterface
     */
    private function prepareExceptions($exceptionMethod = null, $times = 1)
    {
        if ($exceptionMethod !== null) {
            /** @noinspection PhpMethodParametersCountMismatchInspection */
            $this->mockThrower->shouldReceive($exceptionMethod)
                ->times($times)->withNoArgs()->andThrow(new \Exception());
        }

        /** @var ExceptionThrowerInterface $exceptions */
        $exceptions = $this->mockThrower;

        return $exceptions;
    }
}

