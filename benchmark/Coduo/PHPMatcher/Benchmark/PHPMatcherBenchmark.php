<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Benchmark;

use Coduo\PHPMatcher\PHPMatcher;

/**
 * @revs(50)
 * @iterations(10)
 * @outputTimeUnit("milliseconds")
 * @BeforeMethods({"init"})
 */
final class PHPMatcherBenchmark
{
    private PHPMatcher $matcher;

    public function init() : void
    {
        $this->matcher = new PHPMatcher();
    }

    public function bench_php_matching_json() : void
    {
        $this->matcher->match(
            \file_get_contents(__DIR__ . '/github_pulls.json'),
            /* @lang JSON */
            <<<PATTERN
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
  "@...@"
]
PATTERN
        );
    }
}