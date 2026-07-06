"""Applies a newly-validated PHP and/or Laravel version to tests.yml and composer.json.

Uses targeted regex edits rather than a YAML/JSON library round-trip so the resulting
diff only touches the lines that actually changed. A version is only added to a matrix
array if at least one validated combination using it passed; combinations that failed
are recorded as `exclude` entries instead, mirroring the existing php/laravel excludes
in tests.yml.
"""

import json
import re
import sys

TESTS_PATH = ".github/workflows/tests.yml"
COMPOSER_PATH = "composer.json"


def add_to_matrix_array(content, key, value):
    return re.sub(
        rf"({key}: \[)([^\]]*)(\])",
        lambda m: f"{m.group(1)}{m.group(2)}, {value}{m.group(3)}",
        content,
        count=1,
    )


def add_include_entry(content, laravel, testbench):
    include_pattern = re.compile(
        r"(include:\n(?:          - laravel: .*\n            testbench: .*\n)+)"
    )
    return include_pattern.sub(
        lambda m: m.group(1) + f"          - laravel: {laravel}\n            testbench: {testbench}\n",
        content,
        count=1,
    )


def add_exclude_entry(content, php, laravel):
    if re.search(r"^ {8}exclude:\n", content, re.MULTILINE):
        exclude_pattern = re.compile(
            r"( {8}exclude:\n(?: {10}- php: .*\n {12}laravel: .*\n)+)"
        )
        return exclude_pattern.sub(
            lambda m: m.group(1) + f"          - php: {php}\n            laravel: {laravel}\n",
            content,
            count=1,
        )

    # No exclude block exists yet: add one directly after the include block.
    include_pattern = re.compile(
        r"( {8}include:\n(?: {10}- laravel: .*\n {12}testbench: .*\n)+)"
    )
    return include_pattern.sub(
        lambda m: m.group(1) + f"        exclude:\n          - php: {php}\n            laravel: {laravel}\n",
        content,
        count=1,
    )


def bump(new_php, new_laravel, new_testbench, results):
    php_passed = any(r["passed"] for r in results if new_php and r["php"] == new_php)
    laravel_passed = any(r["passed"] for r in results if new_laravel and r["laravel"] == new_laravel)

    with open(TESTS_PATH) as f:
        content = f.read()

    if new_php and php_passed:
        content = add_to_matrix_array(content, "php", new_php)

    if new_laravel and laravel_passed:
        content = add_to_matrix_array(content, "laravel", new_laravel)
        content = add_include_entry(content, new_laravel, new_testbench)

    for result in results:
        if result["passed"]:
            continue
        php_included = result["php"] == new_php and php_passed or result["php"] != new_php
        laravel_included = result["laravel"] == new_laravel and laravel_passed or result["laravel"] != new_laravel
        # Only record an exclude for a combination that is actually reachable in the
        # matrix now, i.e. both its php and laravel values were (or already were) added.
        if php_included and laravel_included:
            content = add_exclude_entry(content, result["php"], result["laravel"])

    with open(TESTS_PATH, "w") as f:
        f.write(content)

    if new_laravel and laravel_passed:
        laravel_major = new_laravel.rstrip(".*")
        with open(COMPOSER_PATH) as f:
            composer = f.read()
        composer = re.sub(
            r'("illuminate/support":\s*")([^"]*)(")',
            lambda m: f"{m.group(1)}{m.group(2)}|^{laravel_major}.0{m.group(3)}",
            composer,
            count=1,
        )
        with open(COMPOSER_PATH, "w") as f:
            f.write(composer)


if __name__ == "__main__":
    new_php, new_laravel, new_testbench, results_path = (sys.argv[1:5] + [""] * 4)[:4]
    with open(results_path) as f:
        results = json.load(f)
    bump(new_php, new_laravel, new_testbench, results)
