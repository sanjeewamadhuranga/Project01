# Themes & customization

Due to the multi-tenant nature of this application, it can be customized to fit tenant branding using themes.

Themes are named based on the tenant company name and are currently including:

- `pay` (default) - Pay
- `dialog` - Dialog / Geniebiz
- `hdbank` - HDBank
- `bankofmaldives` - Bank of Maldives (BML)

Themes can be set on `/configuration/manage/branding` page or by changing the `ADMIN_THEME` setting.
Any invalid value (ie. non-existing theme) will default to `pay`.

## Creating a theme

In order to create a theme, it's best to copy one of the existing ones and customize the colours and icons accordingly.
Themes are automatically recognized by Webpack upon restart without any extra configuration.

```shell
cp asssets/themes/pay assets/themes/new-client
vim assets/themes/new-client/_user-variables.scss # Customize the colours
npm run dev-server # Restart Webpack dev server
```

To allow referencing the theme from code (some business logic depends on this setting), the theme name should be
added to following interface: `\App\Domain\Settings\Theme`.

## Theme directory structure

All themes are stored in `assets/themes` directory and derive from the files in `assets/themes/shared` directory.

Each theme consists of following files:

- `theme.scss` - theme entry point - imports files from `shared` theme, should not be modified
- `_user-variables.scss` - main customization file - colours may be customized here using SCSS and CSS variables
- `_custom.scss` - contains any custom code
- `logo.svg` - the logo that appears on the page - must be in SVG format
- `favicon.ico` - favicon - should be at least 32x32, but it's best to include multiple sizes inside

## Inheritance

Because the themes usually change only colours and paddings, most of the SCSS logic resides in `assets/themes/shared`
directory. Any customizations to the vendor Falcon theme should happen there to be reflected in all themes.
