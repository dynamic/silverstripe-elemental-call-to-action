# Silverstripe Elemental Call to Action

A call to action block for the Elemental block system

[![CI](https://github.com/dynamic/silverstripe-elemental-call-to-action/actions/workflows/ci.yml/badge.svg)](https://github.com/dynamic/silverstripe-elemental-call-to-action/actions/workflows/ci.yml)
[![GitHub Sponsors](https://img.shields.io/github/sponsors/dynamic?label=Sponsors&logo=GitHub%20Sponsors&style=flat&color=ea4aaa)](https://github.com/sponsors/dynamic)

[![Latest Stable Version](https://poser.pugx.org/dynamic/silverstripe-elemental-call-to-action/v/stable)](https://packagist.org/packages/dynamic/silverstripe-elemental-call-to-action)
[![Total Downloads](https://poser.pugx.org/dynamic/silverstripe-elemental-call-to-action/downloads)](https://packagist.org/packages/dynamic/silverstripe-elemental-call-to-action)
[![License](https://poser.pugx.org/dynamic/silverstripe-elemental-call-to-action/license)](https://packagist.org/packages/dynamic/silverstripe-elemental-call-to-action)

## Requirements

* PHP: ^8.3
* SilverStripe: ^6
* dnadesign/silverstripe-elemental: ^6
* silverstripe/linkfield: ^5

## Installation

```sh
composer require dynamic/silverstripe-elemental-call-to-action
```

## License

See [License](LICENSE.md)

## Features

- **Call to Action Block**: Display prominent CTAs with title, description, and customizable link
- **LinkField Integration**: Flexible linking to internal pages, external URLs, files, emails, and phone numbers
- **Bootstrap 5 Templates**: Pre-styled templates based on Bootstrap 5 alert components
- **Elemental Integration**: Seamlessly integrates with SilverStripe Elemental content blocks
- **Content Inheritance**: Extends ElementContent for rich text editing capabilities

## Usage

A block to display a call to action - Title, Description, and Link. The UX is meant to mimic an alert in Bootstrap.

### Template Notes

The default templates are based off [Bootstrap 5](https://getbootstrap.com/) classes/styling

## Getting more elements

See [Elemental modules by Dynamic](https://github.com/orgs/dynamic/repositories?q=elemental&type=all&language=&sort=)

## Configuration

See [SilverStripe Elemental Configuration](https://github.com/silverstripe/silverstripe-elemental#configuration)

## Upgrading from version 2

SilverStripe Elemental Call to Action v3.0 is compatible with SilverStripe 6. Key changes:

- Updated to SilverStripe CMS 6
- Requires PHP 8.3 or higher
- Updated `dnadesign/silverstripe-elemental` from ^5 to ^6
- Updated `silverstripe/linkfield` from ^4 to ^5

See the [SilverStripe 6 Upgrade Guide](https://docs.silverstripe.org/en/6/) for more details.

## Maintainers

 *  [Dynamic](https://www.dynamicagency.com) (<dev@dynamicagency.com>)

## Bugtracker
Bugs are tracked in the issues section of this repository. Before submitting an issue please read over
existing issues to ensure yours is unique.

If the issue does look like a new bug:

 - Create a new issue
 - Describe the steps required to reproduce your issue, and the expected outcome. Unit tests, screenshots
 and screencasts can help here.
 - Describe your environment as detailed as possible: SilverStripe version, Browser, PHP version,
 Operating System, any installed SilverStripe modules.

Please report security issues to the module maintainers directly. Please don't file security issues in the bugtracker.

## Development and contribution
If you would like to make contributions to the module please ensure you raise a pull request and discuss with the module maintainers.
