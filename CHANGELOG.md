# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## Releases

### [0.1.24] - 2024-02-28

* Execute applyChildrenAfter if only one node was found in XpathTextNode

### [0.1.23] - 2024-02-27

* Fix Chunk class: array_combine() must have the same number of elements

### [0.1.22] - 2024-02-27

* Add collection converter HighestNumber

### [0.1.21] - 2024-02-26

* Add scalar converter Combine

### [0.1.20] - 2024-02-26

* Add collection converter Chunk

### [0.1.19] - 2024-02-26

* Add collection converter Concat

### [0.1.18] - 2024-02-26

* Refactoring

### [0.1.17] - 2024-02-26

* Add scalar converter Replace

### [0.1.16] - 2024-02-25

* Add collection converter First

### [0.1.15] - 2024-02-25

* Add collection converter RemoveEmpty
* Converter refactoring: Scalar, Collection

### [0.1.14] - 2024-02-25

* Add first array converter Implode

### [0.1.13] - 2024-02-25

* Add ToLower and ToUpper functions

### [0.1.12] - 2024-02-25

* Add search and replace parameter to Number converter

### [0.1.11] - 2024-02-25

* Fix PregMatch results
  * Find all combinations

### [0.1.10] - 2024-02-25

* Fix Trim converter
  * Return null if null was given

### [0.1.9] - 2024-02-24

* Add Boolean converter

### [0.1.8] - 2024-02-24

* Add LastUrl value
* Refactoring (Add Source, Converter, Output, Value interfaces)

### [0.1.7] - 2024-02-24

* Adds converter PregMatch

### [0.1.6] - 2024-02-24

* Adds englisch number converter

### [0.1.5] - 2024-02-24

* Enable "follow location" within curl command

### [0.1.4] - 2024-02-24

* Add XpathSections examples

### [0.1.3] - 2024-02-24

* Add XpathSection example

### [0.1.2] - 2024-02-24

* Add group example

### [0.1.1] - 2024-02-24

* README.md changes

### [0.1.0] - 2024-02-23

* Initial release with first crawler
* Add src
* Add tests
  * PHP Coding Standards Fixer
  * PHPMND - PHP Magic Number Detector
  * PHPStan - PHP Static Analysis Tool
  * PHPUnit - The PHP Testing Framework
  * Rector - Instant Upgrades and Automated Refactoring
* Add README.md
* Add LICENSE.md

## Add new version

```bash
# Checkout master branch
$ git checkout main && git pull

# Check current version
$ vendor/bin/version-manager --current

# Increase patch version
$ vendor/bin/version-manager --patch

# Change changelog
$ vi CHANGELOG.md

# Push new version
$ git add CHANGELOG.md VERSION && git commit -m "Add version $(cat VERSION)" && git push

# Tag and push new version
$ git tag -a "$(cat VERSION)" -m "Version $(cat VERSION)" && git push origin "$(cat VERSION)"
```
