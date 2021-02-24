<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests;

use Coduo\PHPMatcher\PHPMatcher;
use Coduo\PHPMatcher\PHPUnit\PHPMatcherTestCase;

class MatcherTest extends PHPMatcherTestCase
{
    /**
     * @dataProvider scalarValueExamples
     */
    public function test_matcher_with_scalar_values($value, $pattern) : void
    {
        $this->assertMatchesPattern($pattern, $value);
    }

    public function scalarValueExamples()
    {
        return [
            ['Norbert Orzechowicz', '@string@'],
            [6.66, '@double@'],
            [1, '@integer@'],
            [['foo'], '@array@'],
            ['9f4db639-0e87-4367-9beb-d64e3f42ae18', '@uuid@'],
        ];
    }

    public function test_matcher_with_array_value() : void
    {
        $value = [
            'users' => [
                [
                    'id' => 1,
                    'firstName' => 'Norbert',
                    'lastName' => 'Orzechowicz',
                    'enabled' => true,
                ],
                [
                    'id' => 2,
                    'firstName' => 'Michał',
                    'lastName' => 'Dąbrowski',
                    'enabled' => true,
                ],
            ],
            'readyToUse' => true,
            'data' => new \stdClass(),
        ];

        $pattern = [
            'users' => [
                [
                    'id' => '@integer@',
                    'firstName' => '@string@',
                    'lastName' => 'Orzechowicz',
                    'enabled' => '@boolean@',
                ],
                [
                    'id' => '@integer@',
                    'firstName' => '@string@',
                    'lastName' => 'Dąbrowski',
                    'enabled' => '@boolean@',
                ],
            ],
            'readyToUse' => true,
            'data' => '@wildcard@',
        ];

        $this->assertMatchesPattern($pattern, $value);
    }

    /**
     * @dataProvider jsonDataProvider
     */
    public function test_matcher_with_json($value, $pattern) : void
    {
        $this->assertMatchesPattern($pattern, $value);
    }

    public function jsonDataProvider()
    {
        return [
            [
                '{"data": {"createUserFormSchema":{"formData":"test","schema":"test","uiSchema":"test"}}}',
                '{"data": {"createUserFormSchema":"@json@.hasProperty(\"formData\").hasProperty(\"schema\").hasProperty(\"uiSchema\")"}}',
            ],
            [
                /* @lang JSON */
                '{
                    "users":[
                        {
                            "id": 131,
                            "firstName": "Norbert",
                            "lastName": "Orzechowicz",
                            "enabled": true,
                            "roles": ["ROLE_DEVELOPER"],
                            "createdAt" : "2020-01-01 00:00:00"
                        },
                        {
                            "id": 132,
                            "firstName": "Michał",
                            "lastName": "Dąbrowski",
                            "enabled": false,
                            "roles": ["ROLE_DEVELOPER"],
                            "createdAt" : "2020-01-01 00:00:00"
                        }
                    ],
                    "prevPage": "http:\/\/example.com\/api\/users\/1?limit=2",
                    "nextPage": "http:\/\/example.com\/api\/users\/3?limit=2"
                }',
                '@json@.hasProperty("users")',
            ],
            [
                /* @lang JSON */
                '{
                    "users":[
                        {
                            "id": 131,
                            "firstName": "Norbert",
                            "lastName": "Orzechowicz",
                            "enabled": true,
                            "roles": ["ROLE_DEVELOPER"],
                            "createdAt" : "2020-01-01 00:00:00"
                        },
                        {
                            "id": 132,
                            "firstName": "Michał",
                            "lastName": "Dąbrowski",
                            "enabled": false,
                            "roles": ["ROLE_DEVELOPER"],
                            "createdAt" : "2020-01-01 00:00:00"
                        }
                    ],
                    "prevPage": "http:\/\/example.com\/api\/users\/1?limit=2",
                    "nextPage": "http:\/\/example.com\/api\/users\/3?limit=2"
                }',
                /* @lang JSON */
                '{
                    "users":[
                        {
                            "id": "@integer@",
                            "firstName":"Norbert",
                            "lastName":"Orzechowicz",
                            "enabled": "@boolean@",
                            "roles": "@array@",
                            "createdAt" : "@datetime@"
                        },
                        {
                            "id": "@integer@",
                            "firstName": "Michał",
                            "lastName": "Dąbrowski",
                            "enabled": "expr(value == false)",
                            "roles": "@array@",
                            "createdAt" : "@datetime@.isInDateFormat(\'Y-m-d H:i:s\')"
                        }
                    ],
                    "prevPage": "@string@",
                    "nextPage": "@string@"
                }',
            ],
            [
                /* @lang JSON */
                '{
                    "url": "/accounts/9a7dae2d-d135-4bd7-b202-b3e7e91aaecd"
                }',
                /* @lang JSON */
                '{
                    "url": "/accounts/@uuid@"
                }',
            ],
            [
                /* @lang JSON */
                '{
                    "users":[],
                    "prevPage": "http:\/\/example.com\/api\/users\/1?limit=2",
                    "nextPage": "http:\/\/example.com\/api\/users\/3?limit=2"
                }',
                /* @lang JSON */
                '{
                    "users":[
                        "@...@"
                    ],
                    "prevPage": "@string@",
                    "nextPage": "@string@"
                }',
            ],
            [
                /* @lang JSON */
                '{
                    "users":[
                        {
                            "id": 131,
                            "firstName": "Norbert",
                            "lastName": "Orzechowicz",
                            "enabled": true,
                            "roles": ["ROLE_DEVELOPER"]
                        }
                    ],
                    "prevPage": "http:\/\/example.com\/api\/users\/1?limit=2",
                    "nextPage": "http:\/\/example.com\/api\/users\/3?limit=2"
                }',
                /* @lang JSON */
                '{
                    "users":[
                        {
                            "id": "@integer@",
                            "firstName":"Norbert",
                            "lastName":"Orzechowicz",
                            "enabled": "@boolean@",
                            "roles": "@array@"
                        },
                        "@...@"
                    ],
                    "prevPage": "@string@",
                    "nextPage": "@string@"
                }',
            ],
            [
                /* @lang JSON */
                '{
                    "users":[],
                    "prevPage": "http:\/\/example.com\/api\/users\/1?limit=2",
                    "currPage": 2
                }',
                /* @lang JSON */
                '{
                    "users":[
                        "@...@"
                    ],
                    "prevPage": "@string@.optional()",
                    "nextPage": "@string@.optional()",
                    "currPage": "@integer@.optional()"
                }',
            ],
            [
                /* @lang JSON */
                '{
                    "user": {
                        "id": 131,
                        "firstName": "Norbert",
                        "lastName": "Orzechowicz",
                        "enabled": true,
                        "roles": ["ROLE_DEVELOPER"]
                    }
                }',
                /* @lang JSON */
                '{
                    "user": "@json@"
                }',
            ],
            [
                /* @lang JSON */
                '{
                    "user": null
                }',
                /* @lang JSON */
                '{
                    "user": "@json@.optional()"
                }',
            ],
        ];
    }

    public function test_matcher_with_xml() : void
    {
        $value = <<<'XML'
<?xml version="1.0"?>
<soap:Envelope
xmlns:soap="http://www.w3.org/2001/12/soap-envelope"
soap:encodingStyle="http://www.w3.org/2001/12/soap-encoding">

<soap:Body xmlns:m="http://www.example.org/stock">
  <m:GetStockPrice>
    <m:StockName>IBM</m:StockName>
    <m:StockValue>Any Value</m:StockValue>
  </m:GetStockPrice>
</soap:Body>

</soap:Envelope>
XML;
        $pattern = <<<'XML'
<?xml version="1.0"?>
<soap:Envelope
    xmlns:soap="@string@"
            soap:encodingStyle="@string@">

<soap:Body xmlns:m="@string@">
  <m:GetStockPrice>
    <m:StockName>@string@</m:StockName>
    <m:StockValue>@string@</m:StockValue>
  </m:GetStockPrice>
</soap:Body>

</soap:Envelope>
XML;

        $this->assertMatchesPattern($pattern, $value);
    }

    public function test_matcher_with_xml_including_optional_node() : void
    {
        $value = <<<'XML'
<?xml version="1.0"?>
<soap:Envelope
xmlns:soap="http://www.w3.org/2001/12/soap-envelope"
soap:encodingStyle="http://www.w3.org/2001/12/soap-encoding">

<soap:Body xmlns:m="http://www.example.org/stock">
  <m:GetStockPrice>
    <m:StockName>IBM</m:StockName>
    <m:StockValue>Any Value</m:StockValue>
  </m:GetStockPrice>
</soap:Body>

</soap:Envelope>
XML;
        $pattern = <<<'XML'
<?xml version="1.0"?>
<soap:Envelope
    xmlns:soap="@string@"
            soap:encodingStyle="@string@">

<soap:Body xmlns:m="@string@">
  <m:GetStockPrice>
    <m:StockName>@string@.optional()</m:StockName>
    <m:StockValue>@string@.optional()</m:StockValue>
    <m:StockQty>@integer@.optional()</m:StockQty>
  </m:GetStockPrice>
</soap:Body>

</soap:Envelope>
XML;

        $this->assertMatchesPattern($pattern, $value);
    }

    public function test_full_text_matcher() : void
    {
        $value = 'lorem ipsum 1234 random text';
        $pattern = "@string@.startsWith('lo') ipsum @number@.greaterThan(10) random text";
        $this->assertMatchesPattern($pattern, $value);
    }

    public function test_matcher_with_callback() : void
    {
        $this->assertMatchesPattern(
            fn ($value) => $value === 'test',
            'test'
        );
    }

    public function test_matcher_with_wildcard() : void
    {
        $this->assertMatchesPattern('@*@', 'test');
        $this->assertMatchesPattern('@wildcard@', 'test');
    }

    /**
     * @dataProvider nullExamples
     */
    public function test_null_value_in_the_json(string $value, string $pattern) : void
    {
        $this->assertMatchesPattern($pattern, $value);
    }

    public function nullExamples()
    {
        return [
            [
                '{"proformaInvoiceLink":null}', '{"proformaInvoiceLink":null}',
                '{"proformaInvoiceLink":null, "test":"test"}', '{"proformaInvoiceLink":null, "test":"@string@"}',
                '{"proformaInvoiceLink":null, "test":"test"}', '{"proformaInvoiceLink":@null@, "test":"@string@"}',
            ],
        ];
    }

    public function test_php_pattern_of_github_pull_requests_response() : void
    {
        $this->assertMatchesPattern(
            /* @lang JSON */
            <<<'PATTERN'
[
  {
    "url": "https://api.github.com/repos/coduo/php-matcher/pulls/@number@",
    "id": "@integer@",
    "node_id": "@string@",
    "html_url": "https://github.com/coduo/php-matcher/pull/@integer@",
    "diff_url": "https://github.com/coduo/php-matcher/pull/@integer@.diff",
    "patch_url": "https://github.com/coduo/php-matcher/pull/@integer@.patch",
    "issue_url": "https://api.github.com/repos/coduo/php-matcher/issues/@integer@",
    "number": "@integer@",
    "state": "@string@",
    "locked": "@boolean@",
    "title": "@string@",
    "user": {
      "login": "@string@",
      "id": "@integer@",
      "node_id": "@string@",
      "avatar_url": "@string@",
      "gravatar_id": "@string@",
      "url": "https://api.github.com/users/@string@",
      "html_url": "https://github.com/@string@",
      "followers_url": "https://api.github.com/users/@string@/followers",
      "following_url": "https://api.github.com/users/@string@/following{/other_user}",
      "gists_url": "https://api.github.com/users/@string@/gists{/gist_id}",
      "starred_url": "https://api.github.com/users/@string@/starred{/owner}{/repo}",
      "subscriptions_url": "https://api.github.com/users/@string@/subscriptions",
      "organizations_url": "https://api.github.com/users/@string@/orgs",
      "repos_url": "https://api.github.com/users/@string@/repos",
      "events_url": "https://api.github.com/users/@string@/events{/privacy}",
      "received_events_url": "https://api.github.com/users/@string@/received_events",
      "type": "@string@",
      "site_admin": "@boolean@"
    },
    "body": "@string@",
    "created_at": "@datetime@",
    "updated_at": "@datetime@",
    "closed_at": "@datetime@||@null@",
    "merged_at": "@datetime@||@null@",
    "merge_commit_sha": "@string@",
    "assignee": "@json@||@null@",
    "assignees": "@array@",
    "requested_reviewers": "@array@",
    "requested_teams":"@array@",
    "labels": "@array@",
    "milestone": "@json@||@null@",
    "draft": "@boolean@",
    "commits_url": "https://api.github.com/repos/coduo/php-matcher/pulls/@number@/commits",
    "review_comments_url": "https://api.github.com/repos/coduo/php-matcher/pulls/@number@/comments",
    "review_comment_url": "https://api.github.com/repos/coduo/php-matcher/pulls/comments{/number}",
    "comments_url": "https://api.github.com/repos/coduo/php-matcher/issues/@number@/comments",
    "statuses_url": "https://api.github.com/repos/coduo/php-matcher/statuses/@string@",
    "head": {
      "label": "@string@",
      "ref": "@string@",
      "sha": "@string@",
      "user": {
        "login": "@string@",
        "id": "@integer@",
        "node_id": "@string@",
        "avatar_url": "@string@",
        "gravatar_id": "@string@",
        "url": "https://api.github.com/users/@string@",
        "html_url": "https://github.com/@string@",
        "followers_url": "https://api.github.com/users/@string@/followers",
        "following_url": "https://api.github.com/users/@string@/following{/other_user}",
        "gists_url": "https://api.github.com/users/@string@gists{/gist_id}",
        "starred_url": "https://api.github.com/users/@string@/starred{/owner}{/repo}",
        "subscriptions_url": "https://api.github.com/users/@string@/subscriptions",
        "organizations_url": "https://api.github.com/users/@string@/orgs",
        "repos_url": "https://api.github.com/users/@string@/repos",
        "events_url": "https://api.github.com/users/@string@/events{/privacy}",
        "received_events_url": "https://api.github.com/users/@string@/received_events",
        "type": "@string@",
        "site_admin": "@boolean@"
      },
      "repo": {
        "id": "@integer@",
        "node_id": "@string@",
        "name": "php-matcher",
        "full_name": "@string@",
        "private": "@boolean@",
        "owner": {
          "login": "@string@",
          "id": "@integer@",
          "node_id": "@string@",
          "avatar_url": "@string@",
          "gravatar_id": "@string@",
          "url": "https://api.github.com/users/@string@",
          "html_url": "https://github.com/@string@",
          "followers_url": "https://api.github.com/users/@string@/followers",
          "following_url": "https://api.github.com/users/@string@/following{/other_user}",
          "gists_url": "https://api.github.com/users/@string@/gists{/gist_id}",
          "starred_url": "https://api.github.com/users/@string@/starred{/owner}{/repo}",
          "subscriptions_url": "https://api.github.com/users/@string@/subscriptions",
          "organizations_url": "https://api.github.com/users/@string@/orgs",
          "repos_url": "https://api.github.com/users/@string@/repos",
          "events_url": "https://api.github.com/users/@string@/events{/privacy}",
          "received_events_url": "https://api.github.com/users/@string@/received_events",
          "type": "@string@",
          "site_admin": "@boolean@"
        },
        "html_url": "https://github.com/@string@/php-matcher",
        "description": "@string@",
        "fork": "@boolean@",
        "url": "https://api.github.com/repos/@string@/php-matcher",
        "forks_url": "https://api.github.com/repos/@string@/php-matcher/forks",
        "keys_url": "https://api.github.com/repos/@string@/php-matcher/keys{/key_id}",
        "collaborators_url": "https://api.github.com/repos/@string@/php-matcher/collaborators{/collaborator}",
        "teams_url": "https://api.github.com/repos/@string@/php-matcher/teams",
        "hooks_url": "https://api.github.com/repos/@string@/php-matcher/hooks",
        "issue_events_url": "https://api.github.com/repos/@string@/php-matcher/issues/events{/number}",
        "events_url": "https://api.github.com/repos/@string@/php-matcher/events",
        "assignees_url": "https://api.github.com/repos/@string@/php-matcher/assignees{/user}",
        "branches_url": "https://api.github.com/repos/@string@/php-matcher/branches{/branch}",
        "tags_url": "https://api.github.com/repos/@string@/php-matcher/tags",
        "blobs_url": "https://api.github.com/repos/@string@/php-matcher/git/blobs{/sha}",
        "git_tags_url": "https://api.github.com/repos/@string@/php-matcher/git/tags{/sha}",
        "git_refs_url": "https://api.github.com/repos/@string@/php-matcher/git/refs{/sha}",
        "trees_url": "https://api.github.com/repos/@string@/php-matcher/git/trees{/sha}",
        "statuses_url": "https://api.github.com/repos/@string@/php-matcher/statuses/{sha}",
        "languages_url": "https://api.github.com/repos/@string@/php-matcher/languages",
        "stargazers_url": "https://api.github.com/repos/@string@/php-matcher/stargazers",
        "contributors_url": "https://api.github.com/repos/@string@/php-matcher/contributors",
        "subscribers_url": "https://api.github.com/repos/@string@/php-matcher/subscribers",
        "subscription_url": "https://api.github.com/repos/@string@/php-matcher/subscription",
        "commits_url": "https://api.github.com/repos/@string@/php-matcher/commits{/sha}",
        "git_commits_url": "https://api.github.com/repos/@string@/php-matcher/git/commits{/sha}",
        "comments_url": "https://api.github.com/repos/@string@/php-matcher/comments{/number}",
        "issue_comment_url": "https://api.github.com/repos/@string@/php-matcher/issues/comments{/number}",
        "contents_url": "https://api.github.com/repos/@string@/php-matcher/contents/{+path}",
        "compare_url": "https://api.github.com/repos/@string@/php-matcher/compare/{base}...{head}",
        "merges_url": "https://api.github.com/repos/@string@/php-matcher/merges",
        "archive_url": "https://api.github.com/repos/@string@/php-matcher/{archive_format}{/ref}",
        "downloads_url": "https://api.github.com/repos/@string@/php-matcher/downloads",
        "issues_url": "https://api.github.com/repos/@string@/php-matcher/issues{/number}",
        "pulls_url": "https://api.github.com/repos/@string@/php-matcher/pulls{/number}",
        "milestones_url": "https://api.github.com/repos/@string@/php-matcher/milestones{/number}",
        "notifications_url": "https://api.github.com/repos/@string@/php-matcher/notifications{?since,all,participating}",
        "labels_url": "https://api.github.com/repos/@string@/php-matcher/labels{/name}",
        "releases_url": "https://api.github.com/repos/@string@/php-matcher/releases{/id}",
        "deployments_url": "https://api.github.com/repos/@string@/php-matcher/deployments",
        "created_at": "@datetime@",
        "updated_at": "@datetime@",
        "pushed_at": "@datetime@",
        "git_url": "git://github.com/@string@/php-matcher.git",
        "ssh_url": "git@github.com:@string@/php-matcher.git",
        "clone_url": "https://github.com/@string@/php-matcher.git",
        "svn_url": "https://github.com/@string@/php-matcher",
        "homepage": "@string@",
        "size": "@integer@",
        "stargazers_count": "@integer@",
        "watchers_count": "@integer@",
        "language": "@string@||@null@",
        "has_issues": "@boolean@",
        "has_projects": "@boolean@",
        "has_downloads": "@boolean@",
        "has_wiki": "@boolean@",
        "has_pages": "@boolean@",
        "forks_count": "@integer@",
        "mirror_url": "@string@.isUrl()||@null@",
        "archived": "@boolean@",
        "disabled": "@boolean@",
        "open_issues_count": "@integer@",
        "license": "@json@",
        "forks": "@integer@",
        "open_issues": "@integer@",
        "watchers": "@integer@",
        "default_branch": "@string@"
      }
    },
    "base": {
      "label": "@string@",
      "ref": "@string@",
      "sha": "@string@",
      "user": {
        "login": "coduo",
        "id": "@integer@",
        "node_id": "@string@",
        "avatar_url": "@string@",
        "gravatar_id": "@string@||@null@",
        "url": "https://api.github.com/users/coduo",
        "html_url": "https://github.com/coduo",
        "followers_url": "https://api.github.com/users/coduo/followers",
        "following_url": "https://api.github.com/users/coduo/following{/other_user}",
        "gists_url": "https://api.github.com/users/coduo/gists{/gist_id}",
        "starred_url": "https://api.github.com/users/coduo/starred{/owner}{/repo}",
        "subscriptions_url": "https://api.github.com/users/coduo/subscriptions",
        "organizations_url": "https://api.github.com/users/coduo/orgs",
        "repos_url": "https://api.github.com/users/coduo/repos",
        "events_url": "https://api.github.com/users/coduo/events{/privacy}",
        "received_events_url": "https://api.github.com/users/coduo/received_events",
        "type": "Organization",
        "site_admin": "@boolean@"
      },
      "repo": {
        "id": "@integer@",
        "node_id": "@string@",
        "name": "php-matcher",
        "full_name": "coduo/php-matcher",
        "private": "@boolean@",
        "owner": {
          "login": "coduo",
          "id": "@integer@",
          "node_id": "@string@",
          "avatar_url": "@string@",
          "gravatar_id": "@string@",
          "url": "https://api.github.com/users/coduo",
          "html_url": "https://github.com/coduo",
          "followers_url": "https://api.github.com/users/coduo/followers",
          "following_url": "https://api.github.com/users/coduo/following{/other_user}",
          "gists_url": "https://api.github.com/users/coduo/gists{/gist_id}",
          "starred_url": "https://api.github.com/users/coduo/starred{/owner}{/repo}",
          "subscriptions_url": "https://api.github.com/users/coduo/subscriptions",
          "organizations_url": "https://api.github.com/users/coduo/orgs",
          "repos_url": "https://api.github.com/users/coduo/repos",
          "events_url": "https://api.github.com/users/coduo/events{/privacy}",
          "received_events_url": "https://api.github.com/users/coduo/received_events",
          "type": "@string@",
          "site_admin": "@boolean@"
        },
        "html_url": "https://github.com/coduo/php-matcher",
        "description": "@string@",
        "fork": "@boolean@",
        "url": "https://api.github.com/repos/coduo/php-matcher",
        "forks_url": "https://api.github.com/repos/coduo/php-matcher/forks",
        "keys_url": "https://api.github.com/repos/coduo/php-matcher/keys{/key_id}",
        "collaborators_url": "https://api.github.com/repos/coduo/php-matcher/collaborators{/collaborator}",
        "teams_url": "https://api.github.com/repos/coduo/php-matcher/teams",
        "hooks_url": "https://api.github.com/repos/coduo/php-matcher/hooks",
        "issue_events_url": "https://api.github.com/repos/coduo/php-matcher/issues/events{/number}",
        "events_url": "https://api.github.com/repos/coduo/php-matcher/events",
        "assignees_url": "https://api.github.com/repos/coduo/php-matcher/assignees{/user}",
        "branches_url": "https://api.github.com/repos/coduo/php-matcher/branches{/branch}",
        "tags_url": "https://api.github.com/repos/coduo/php-matcher/tags",
        "blobs_url": "https://api.github.com/repos/coduo/php-matcher/git/blobs{/sha}",
        "git_tags_url": "https://api.github.com/repos/coduo/php-matcher/git/tags{/sha}",
        "git_refs_url": "https://api.github.com/repos/coduo/php-matcher/git/refs{/sha}",
        "trees_url": "https://api.github.com/repos/coduo/php-matcher/git/trees{/sha}",
        "statuses_url": "https://api.github.com/repos/coduo/php-matcher/statuses/{sha}",
        "languages_url": "https://api.github.com/repos/coduo/php-matcher/languages",
        "stargazers_url": "https://api.github.com/repos/coduo/php-matcher/stargazers",
        "contributors_url": "https://api.github.com/repos/coduo/php-matcher/contributors",
        "subscribers_url": "https://api.github.com/repos/coduo/php-matcher/subscribers",
        "subscription_url": "https://api.github.com/repos/coduo/php-matcher/subscription",
        "commits_url": "https://api.github.com/repos/coduo/php-matcher/commits{/sha}",
        "git_commits_url": "https://api.github.com/repos/coduo/php-matcher/git/commits{/sha}",
        "comments_url": "https://api.github.com/repos/coduo/php-matcher/comments{/number}",
        "issue_comment_url": "https://api.github.com/repos/coduo/php-matcher/issues/comments{/number}",
        "contents_url": "https://api.github.com/repos/coduo/php-matcher/contents/{+path}",
        "compare_url": "https://api.github.com/repos/coduo/php-matcher/compare/{base}...{head}",
        "merges_url": "https://api.github.com/repos/coduo/php-matcher/merges",
        "archive_url": "https://api.github.com/repos/coduo/php-matcher/{archive_format}{/ref}",
        "downloads_url": "https://api.github.com/repos/coduo/php-matcher/downloads",
        "issues_url": "https://api.github.com/repos/coduo/php-matcher/issues{/number}",
        "pulls_url": "https://api.github.com/repos/coduo/php-matcher/pulls{/number}",
        "milestones_url": "https://api.github.com/repos/coduo/php-matcher/milestones{/number}",
        "notifications_url": "https://api.github.com/repos/coduo/php-matcher/notifications{?since,all,participating}",
        "labels_url": "https://api.github.com/repos/coduo/php-matcher/labels{/name}",
        "releases_url": "https://api.github.com/repos/coduo/php-matcher/releases{/id}",
        "deployments_url": "https://api.github.com/repos/coduo/php-matcher/deployments",
        "created_at": "@datetime@",
        "updated_at": "@datetime@",
        "pushed_at": "@datetime@",
        "git_url": "@string@",
        "ssh_url": "@string@",
        "clone_url":  "@string@.isUrl()",
        "svn_url":  "@string@.isUrl()",
        "homepage":  "@string@||@null@",
        "size": "@integer@",
        "stargazers_count": "@integer@",
        "watchers_count": "@integer@",
        "language": "@string@",
        "has_issues": "@boolean@",
        "has_projects": "@boolean@",
        "has_downloads": "@boolean@",
        "has_wiki": "@boolean@",
        "has_pages": "@boolean@",
        "forks_count": "@integer@",
        "mirror_url": "@string@||@null@",
        "archived": "@boolean@",
        "disabled": "@boolean@",
        "open_issues_count": "@integer@",
        "license": "@json@",
        "forks": "@integer@",
        "open_issues": "@integer@",
        "watchers": "@integer@",
        "default_branch": "@string@"
      }
    },
    "_links": {
      "self": {
        "href": "https://api.github.com/repos/coduo/php-matcher/pulls/@integer@"
      },
      "html": {
        "href": "https://github.com/coduo/php-matcher/pull/@integer@"
      },
      "issue": {
        "href": "https://api.github.com/repos/coduo/php-matcher/issues/@integer@"
      },
      "comments": {
        "href": "https://api.github.com/repos/coduo/php-matcher/issues/@integer@/comments"
      },
      "review_comments": {
        "href": "https://api.github.com/repos/coduo/php-matcher/pulls/@integer@/comments"
      },
      "review_comment": {
        "href": "https://api.github.com/repos/coduo/php-matcher/pulls/comments{/number}"
      },
      "commits": {
        "href": "https://api.github.com/repos/coduo/php-matcher/pulls/@integer@/commits"
      },
      "statuses": {
        "href": "https://api.github.com/repos/coduo/php-matcher/statuses/@string@"
      }
    },
    "author_association": "@string@",
    "active_lock_reason": null
  },
  "@array_previous_repeat@"
]
PATTERN,
            \file_get_contents(__DIR__ . '/fixtures/github_pulls.json'),
        );
    }

    public function test_php_pattern_of_github_pull_requests_response_with_one_small_mistake() : void
    {
        $matcher = new PHPMatcher();

        $matcher->match(
            \file_get_contents(__DIR__ . '/fixtures/github_pulls.json'),
            /* @lang JSON */
            <<<'PATTERN'
[
  {
    "url": "https://api.github.com/repos/coduo/php-matcher/pulls/@number@",
    "id": "@integer@",
    "node_id": "@string@",
    "html_url": "https://github.com/coduo/php-matcher/pull/@integer@",
    "diff_url": "https://github.com/coduo/php-matcher/pull/@integer@.diff",
    "patch_url": "https://github.com/coduo/php-matcher/pull/@integer@.patch",
    "issue_url": "https://api.github.com/repos/coduo/php-matcher/issues/@integer@",
    "number": "@integer@",
    "state": "@string@",
    "locked": "@boolean@",
    "title": "@string@",
    "user": {
      "login": "@integer@",
      "id": "@integer@",
      "node_id": "@string@",
      "avatar_url": "@string@",
      "gravatar_id": "@string@",
      "url": "https://api.github.com/users/@string@",
      "html_url": "https://github.com/@string@",
      "followers_url": "https://api.github.com/users/@string@/followers",
      "following_url": "https://api.github.com/users/@string@/following{/other_user}",
      "gists_url": "https://api.github.com/users/@string@/gists{/gist_id}",
      "starred_url": "https://api.github.com/users/@string@/starred{/owner}{/repo}",
      "subscriptions_url": "https://api.github.com/users/@string@/subscriptions",
      "organizations_url": "https://api.github.com/users/@string@/orgs",
      "repos_url": "https://api.github.com/users/@string@/repos",
      "events_url": "https://api.github.com/users/@string@/events{/privacy}",
      "received_events_url": "https://api.github.com/users/@string@/received_events",
      "type": "@string@",
      "site_admin": "@boolean@"
    },
    "body": "@string@",
    "created_at": "@datetime@",
    "updated_at": "@datetime@",
    "closed_at": "@datetime@||@null@",
    "merged_at": "@datetime@||@null@",
    "merge_commit_sha": "@string@",
    "assignee": "@json@||@null@",
    "assignees": "@array@",
    "requested_reviewers": "@array@",
    "requested_teams":"@array@",
    "labels": "@array@",
    "milestone": "@json@||@null@",
    "draft": "@boolean@",
    "commits_url": "https://api.github.com/repos/coduo/php-matcher/pulls/@number@/commits",
    "review_comments_url": "https://api.github.com/repos/coduo/php-matcher/pulls/@number@/comments",
    "review_comment_url": "https://api.github.com/repos/coduo/php-matcher/pulls/comments{/number}",
    "comments_url": "https://api.github.com/repos/coduo/php-matcher/issues/@number@/comments",
    "statuses_url": "https://api.github.com/repos/coduo/php-matcher/statuses/@string@",
    "head": {
      "label": "@string@",
      "ref": "@string@",
      "sha": "@string@",
      "user": {
        "login": "@string@",
        "id": "@integer@",
        "node_id": "@string@",
        "avatar_url": "@string@",
        "gravatar_id": "@string@",
        "url": "https://api.github.com/users/@string@",
        "html_url": "https://github.com/@string@",
        "followers_url": "https://api.github.com/users/@string@/followers",
        "following_url": "https://api.github.com/users/@string@/following{/other_user}",
        "gists_url": "https://api.github.com/users/@string@gists{/gist_id}",
        "starred_url": "https://api.github.com/users/@string@/starred{/owner}{/repo}",
        "subscriptions_url": "https://api.github.com/users/@string@/subscriptions",
        "organizations_url": "https://api.github.com/users/@string@/orgs",
        "repos_url": "https://api.github.com/users/@string@/repos",
        "events_url": "https://api.github.com/users/@string@/events{/privacy}",
        "received_events_url": "https://api.github.com/users/@string@/received_events",
        "type": "@string@",
        "site_admin": "@boolean@"
      },
      "repo": {
        "id": "@integer@",
        "node_id": "@string@",
        "name": "php-matcher",
        "full_name": "@string@",
        "private": "@boolean@",
        "owner": {
          "login": "@string@",
          "id": "@integer@",
          "node_id": "@string@",
          "avatar_url": "@string@",
          "gravatar_id": "@string@",
          "url": "https://api.github.com/users/@string@",
          "html_url": "https://github.com/@string@",
          "followers_url": "https://api.github.com/users/@string@/followers",
          "following_url": "https://api.github.com/users/@string@/following{/other_user}",
          "gists_url": "https://api.github.com/users/@string@/gists{/gist_id}",
          "starred_url": "https://api.github.com/users/@string@/starred{/owner}{/repo}",
          "subscriptions_url": "https://api.github.com/users/@string@/subscriptions",
          "organizations_url": "https://api.github.com/users/@string@/orgs",
          "repos_url": "https://api.github.com/users/@string@/repos",
          "events_url": "https://api.github.com/users/@string@/events{/privacy}",
          "received_events_url": "https://api.github.com/users/@string@/received_events",
          "type": "@string@",
          "site_admin": "@boolean@"
        },
        "html_url": "https://github.com/@string@/php-matcher",
        "description": "@string@",
        "fork": "@boolean@",
        "url": "https://api.github.com/repos/@string@/php-matcher",
        "forks_url": "https://api.github.com/repos/@string@/php-matcher/forks",
        "keys_url": "https://api.github.com/repos/@string@/php-matcher/keys{/key_id}",
        "collaborators_url": "https://api.github.com/repos/@string@/php-matcher/collaborators{/collaborator}",
        "teams_url": "https://api.github.com/repos/@string@/php-matcher/teams",
        "hooks_url": "https://api.github.com/repos/@string@/php-matcher/hooks",
        "issue_events_url": "https://api.github.com/repos/@string@/php-matcher/issues/events{/number}",
        "events_url": "https://api.github.com/repos/@string@/php-matcher/events",
        "assignees_url": "https://api.github.com/repos/@string@/php-matcher/assignees{/user}",
        "branches_url": "https://api.github.com/repos/@string@/php-matcher/branches{/branch}",
        "tags_url": "https://api.github.com/repos/@string@/php-matcher/tags",
        "blobs_url": "https://api.github.com/repos/@string@/php-matcher/git/blobs{/sha}",
        "git_tags_url": "https://api.github.com/repos/@string@/php-matcher/git/tags{/sha}",
        "git_refs_url": "https://api.github.com/repos/@string@/php-matcher/git/refs{/sha}",
        "trees_url": "https://api.github.com/repos/@string@/php-matcher/git/trees{/sha}",
        "statuses_url": "https://api.github.com/repos/@string@/php-matcher/statuses/{sha}",
        "languages_url": "https://api.github.com/repos/@string@/php-matcher/languages",
        "stargazers_url": "https://api.github.com/repos/@string@/php-matcher/stargazers",
        "contributors_url": "https://api.github.com/repos/@string@/php-matcher/contributors",
        "subscribers_url": "https://api.github.com/repos/@string@/php-matcher/subscribers",
        "subscription_url": "https://api.github.com/repos/@string@/php-matcher/subscription",
        "commits_url": "https://api.github.com/repos/@string@/php-matcher/commits{/sha}",
        "git_commits_url": "https://api.github.com/repos/@string@/php-matcher/git/commits{/sha}",
        "comments_url": "https://api.github.com/repos/@string@/php-matcher/comments{/number}",
        "issue_comment_url": "https://api.github.com/repos/@string@/php-matcher/issues/comments{/number}",
        "contents_url": "https://api.github.com/repos/@string@/php-matcher/contents/{+path}",
        "compare_url": "https://api.github.com/repos/@string@/php-matcher/compare/{base}...{head}",
        "merges_url": "https://api.github.com/repos/@string@/php-matcher/merges",
        "archive_url": "https://api.github.com/repos/@string@/php-matcher/{archive_format}{/ref}",
        "downloads_url": "https://api.github.com/repos/@string@/php-matcher/downloads",
        "issues_url": "https://api.github.com/repos/@string@/php-matcher/issues{/number}",
        "pulls_url": "https://api.github.com/repos/@string@/php-matcher/pulls{/number}",
        "milestones_url": "https://api.github.com/repos/@string@/php-matcher/milestones{/number}",
        "notifications_url": "https://api.github.com/repos/@string@/php-matcher/notifications{?since,all,participating}",
        "labels_url": "https://api.github.com/repos/@string@/php-matcher/labels{/name}",
        "releases_url": "https://api.github.com/repos/@string@/php-matcher/releases{/id}",
        "deployments_url": "https://api.github.com/repos/@string@/php-matcher/deployments",
        "created_at": "@datetime@",
        "updated_at": "@datetime@",
        "pushed_at": "@datetime@",
        "git_url": "git://github.com/@string@/php-matcher.git",
        "ssh_url": "git@github.com:@string@/php-matcher.git",
        "clone_url": "https://github.com/@string@/php-matcher.git",
        "svn_url": "https://github.com/@string@/php-matcher",
        "homepage": "@string@",
        "size": "@integer@",
        "stargazers_count": "@integer@",
        "watchers_count": "@integer@",
        "language": "@string@||@null@",
        "has_issues": "@boolean@",
        "has_projects": "@boolean@",
        "has_downloads": "@boolean@",
        "has_wiki": "@boolean@",
        "has_pages": "@boolean@",
        "forks_count": "@integer@",
        "mirror_url": "@string@.isUrl()||@null@",
        "archived": "@boolean@",
        "disabled": "@boolean@",
        "open_issues_count": "@integer@",
        "license": "@json@",
        "forks": "@integer@",
        "open_issues": "@integer@",
        "watchers": "@integer@",
        "default_branch": "@string@"
      }
    },
    "base": {
      "label": "@string@",
      "ref": "@string@",
      "sha": "@string@",
      "user": {
        "login": "coduo",
        "id": "@integer@",
        "node_id": "@string@",
        "avatar_url": "@string@",
        "gravatar_id": "@string@||@null@",
        "url": "https://api.github.com/users/coduo",
        "html_url": "https://github.com/coduo",
        "followers_url": "https://api.github.com/users/coduo/followers",
        "following_url": "https://api.github.com/users/coduo/following{/other_user}",
        "gists_url": "https://api.github.com/users/coduo/gists{/gist_id}",
        "starred_url": "https://api.github.com/users/coduo/starred{/owner}{/repo}",
        "subscriptions_url": "https://api.github.com/users/coduo/subscriptions",
        "organizations_url": "https://api.github.com/users/coduo/orgs",
        "repos_url": "https://api.github.com/users/coduo/repos",
        "events_url": "https://api.github.com/users/coduo/events{/privacy}",
        "received_events_url": "https://api.github.com/users/coduo/received_events",
        "type": "Organization",
        "site_admin": "@boolean@"
      },
      "repo": {
        "id": "@integer@",
        "node_id": "@string@",
        "name": "php-matcher",
        "full_name": "coduo/php-matcher",
        "private": "@boolean@",
        "owner": {
          "login": "coduo",
          "id": "@integer@",
          "node_id": "@string@",
          "avatar_url": "@string@",
          "gravatar_id": "@string@",
          "url": "https://api.github.com/users/coduo",
          "html_url": "https://github.com/coduo",
          "followers_url": "https://api.github.com/users/coduo/followers",
          "following_url": "https://api.github.com/users/coduo/following{/other_user}",
          "gists_url": "https://api.github.com/users/coduo/gists{/gist_id}",
          "starred_url": "https://api.github.com/users/coduo/starred{/owner}{/repo}",
          "subscriptions_url": "https://api.github.com/users/coduo/subscriptions",
          "organizations_url": "https://api.github.com/users/coduo/orgs",
          "repos_url": "https://api.github.com/users/coduo/repos",
          "events_url": "https://api.github.com/users/coduo/events{/privacy}",
          "received_events_url": "https://api.github.com/users/coduo/received_events",
          "type": "@string@",
          "site_admin": "@boolean@"
        },
        "html_url": "https://github.com/coduo/php-matcher",
        "description": "@string@",
        "fork": "@boolean@",
        "url": "https://api.github.com/repos/coduo/php-matcher",
        "forks_url": "https://api.github.com/repos/coduo/php-matcher/forks",
        "keys_url": "https://api.github.com/repos/coduo/php-matcher/keys{/key_id}",
        "collaborators_url": "https://api.github.com/repos/coduo/php-matcher/collaborators{/collaborator}",
        "teams_url": "https://api.github.com/repos/coduo/php-matcher/teams",
        "hooks_url": "https://api.github.com/repos/coduo/php-matcher/hooks",
        "issue_events_url": "https://api.github.com/repos/coduo/php-matcher/issues/events{/number}",
        "events_url": "https://api.github.com/repos/coduo/php-matcher/events",
        "assignees_url": "https://api.github.com/repos/coduo/php-matcher/assignees{/user}",
        "branches_url": "https://api.github.com/repos/coduo/php-matcher/branches{/branch}",
        "tags_url": "https://api.github.com/repos/coduo/php-matcher/tags",
        "blobs_url": "https://api.github.com/repos/coduo/php-matcher/git/blobs{/sha}",
        "git_tags_url": "https://api.github.com/repos/coduo/php-matcher/git/tags{/sha}",
        "git_refs_url": "https://api.github.com/repos/coduo/php-matcher/git/refs{/sha}",
        "trees_url": "https://api.github.com/repos/coduo/php-matcher/git/trees{/sha}",
        "statuses_url": "https://api.github.com/repos/coduo/php-matcher/statuses/{sha}",
        "languages_url": "https://api.github.com/repos/coduo/php-matcher/languages",
        "stargazers_url": "https://api.github.com/repos/coduo/php-matcher/stargazers",
        "contributors_url": "https://api.github.com/repos/coduo/php-matcher/contributors",
        "subscribers_url": "https://api.github.com/repos/coduo/php-matcher/subscribers",
        "subscription_url": "https://api.github.com/repos/coduo/php-matcher/subscription",
        "commits_url": "https://api.github.com/repos/coduo/php-matcher/commits{/sha}",
        "git_commits_url": "https://api.github.com/repos/coduo/php-matcher/git/commits{/sha}",
        "comments_url": "https://api.github.com/repos/coduo/php-matcher/comments{/number}",
        "issue_comment_url": "https://api.github.com/repos/coduo/php-matcher/issues/comments{/number}",
        "contents_url": "https://api.github.com/repos/coduo/php-matcher/contents/{+path}",
        "compare_url": "https://api.github.com/repos/coduo/php-matcher/compare/{base}...{head}",
        "merges_url": "https://api.github.com/repos/coduo/php-matcher/merges",
        "archive_url": "https://api.github.com/repos/coduo/php-matcher/{archive_format}{/ref}",
        "downloads_url": "https://api.github.com/repos/coduo/php-matcher/downloads",
        "issues_url": "https://api.github.com/repos/coduo/php-matcher/issues{/number}",
        "pulls_url": "https://api.github.com/repos/coduo/php-matcher/pulls{/number}",
        "milestones_url": "https://api.github.com/repos/coduo/php-matcher/milestones{/number}",
        "notifications_url": "https://api.github.com/repos/coduo/php-matcher/notifications{?since,all,participating}",
        "labels_url": "https://api.github.com/repos/coduo/php-matcher/labels{/name}",
        "releases_url": "https://api.github.com/repos/coduo/php-matcher/releases{/id}",
        "deployments_url": "https://api.github.com/repos/coduo/php-matcher/deployments",
        "created_at": "@datetime@",
        "updated_at": "@datetime@",
        "pushed_at": "@datetime@",
        "git_url": "@string@",
        "ssh_url": "@string@",
        "clone_url":  "@string@.isUrl()",
        "svn_url":  "@string@.isUrl()",
        "homepage":  "@string@||@null@",
        "size": "@integer@",
        "stargazers_count": "@integer@",
        "watchers_count": "@integer@",
        "language": "@string@",
        "has_issues": "@boolean@",
        "has_projects": "@boolean@",
        "has_downloads": "@boolean@",
        "has_wiki": "@boolean@",
        "has_pages": "@boolean@",
        "forks_count": "@integer@",
        "mirror_url": "@string@||@null@",
        "archived": "@boolean@",
        "disabled": "@boolean@",
        "open_issues_count": "@integer@",
        "license": "@json@",
        "forks": "@integer@",
        "open_issues": "@integer@",
        "watchers": "@integer@",
        "default_branch": "@string@"
      }
    },
    "_links": {
      "self": {
        "href": "https://api.github.com/repos/coduo/php-matcher/pulls/@integer@"
      },
      "html": {
        "href": "https://github.com/coduo/php-matcher/pull/@integer@"
      },
      "issue": {
        "href": "https://api.github.com/repos/coduo/php-matcher/issues/@integer@"
      },
      "comments": {
        "href": "https://api.github.com/repos/coduo/php-matcher/issues/@integer@/comments"
      },
      "review_comments": {
        "href": "https://api.github.com/repos/coduo/php-matcher/pulls/@integer@/comments"
      },
      "review_comment": {
        "href": "https://api.github.com/repos/coduo/php-matcher/pulls/comments{/number}"
      },
      "commits": {
        "href": "https://api.github.com/repos/coduo/php-matcher/pulls/@integer@/commits"
      },
      "statuses": {
        "href": "https://api.github.com/repos/coduo/php-matcher/statuses/@string@"
      }
    },
    "author_association": "@string@",
    "active_lock_reason": null
  },
  "@array_previous_repeat@"
]
PATTERN
        );

        $this->assertSame(
            'Value "norberttech" does not match pattern "@integer@" at path: "[0][user][login]"',
            $matcher->error()
        );
    }
}
