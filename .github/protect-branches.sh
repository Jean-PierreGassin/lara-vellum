#!/usr/bin/env bash
#
# Apply branch protection to main and dev.
#
# Workflows can only RUN the checks; requiring a PR and requiring those checks
# to pass before merge is a repository setting. Run this once after the repo
# exists on GitHub and the `tests` / `quality` workflows have run at least once
# (so their check names are registered).
#
# Requires: gh CLI, authenticated with admin rights on the repo.
# Usage: ./.github/protect-branches.sh <owner/repo>

set -euo pipefail

REPO="${1:-$(gh repo view --json nameWithOwner -q .nameWithOwner)}"

# Status check contexts, exactly as they appear on a PR's checks tab.
CHECKS='[
  {"context":"PHP 8.3 - Laravel 12.*"},
  {"context":"PHP 8.4 - Laravel 12.*"},
  {"context":"Coding standards (PER-CS)"},
  {"context":"Static analysis (PHPStan)"}
]'

for BRANCH in main dev; do
  echo "Protecting $BRANCH on $REPO ..."
  gh api \
    --method PUT \
    -H "Accept: application/vnd.github+json" \
    "repos/$REPO/branches/$BRANCH/protection" \
    --input - <<JSON
{
  "required_status_checks": {
    "strict": true,
    "checks": $CHECKS
  },
  "enforce_admins": true,
  "required_pull_request_reviews": {
    "required_approving_review_count": 1,
    "dismiss_stale_reviews": true
  },
  "restrictions": null,
  "required_linear_history": true,
  "allow_force_pushes": false,
  "allow_deletions": false
}
JSON
done

echo "Done. main and dev now require a passing PR to merge."
