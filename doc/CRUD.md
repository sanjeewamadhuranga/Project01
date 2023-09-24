# Simple CRUD implementation

To quickly achieve repetitive CRUD tasks, it is recommended to extend `CrudController` which provides common logic for:

- Permission checks
- Form handling with validation
- List handling
- Deleting items
- Showing single item details

This is achieved trough `BasicCrudController` and the traits for each action:

- `IndexAction` - shows index page with embedded WebComponent for the list of items. Route: `GET /`
- `ListAction` - provides list endpoint for AJAX tables. Route: `GET /list`
- `ShowAction` - displays single item details. Route: `GET /{id}`
- `DeleteAction` - provides soft-delete functionality. Route: `DELETE /{id}`
- `CreateAction` - displays and handles item creation form. Route: `GET|POST /create`
- `UpdateAction` - displays and handles item edit form. Route: `GET|POST /{id}/edit`
- `StatusAction` (optional) - allows toggling item status. Item must implement `Activeable` interface. Route: `PUT /{id}/status/{active}`

While providing a simple interface with only few methods required, it offers enough flexibility to add more features and/or replace existing logic.

## Getting started

### Form type

To start, create a form type that will handle your item (document) in create/update actions.
Please note that the form type should support passing the document as data. Make sure you add `data_class` option for autocompletion.

### Controller

Create a `Controller` extending `CrudController` and implement following methods:

- `getFormType()` - returning the form type class name used in create/update actions (created above)
- `getItemClass()` - returning the item class name (document) you wish the controller to handle

### CRUD key

Each CRUD should contain unique "key" specified in `getKey()` method.
The key is used to generate route names, template paths and permission names to authorize user in actions.
While implementing `getKey` method is optional (it generates the key from item class name), it is recommended to implement it to avoid confusion.

### Templates

Create `show.html.twig` in `templates/{key}/` directory (this step is mandatory).

There are default templates for `index` and `create/update` actions in `common/crud/index.html.twig` and `common/crud/form.html.twig`
but you can override it with creating `templates/{key}/index.html.twig` and `templates/{key}/form.html.twig`. You may extend `createUpdate.html.twig` to avoid duplication.

When rendering the template, CRUD controllers check for custom `form` and `index` template existence and render the default one if they are missing.
This is to reduce code duplication and make it easier to create new CRUDs.

### IndexAction component and props

`IndexAction` trait by default constructs component name for listing from `{key}` by replacing `.` and `_` characters
with `-` and appending `-list`. E.g. for key `test_document` it constructs `test-document-list` component name.
You can customize that calling `getIndexComponent(): ?string`.

Additionally, if that component requires some props you can
provide them with calling `getIndexComponentProps(Request $request): array`.

### Back Url

`create` and `update` actions requires url to go `back` (e.g. to `index` action). For such thing the `CreateOrUpdateTrait`
has `getBackUrl(): ?string` method which by default returns url to `index` action for that specific document. You can
override that method in your controller.

### Titles

Default translation keys for actions like `index`, `create` and `update` are created from
`{key}.title.{action}` e.g. `test_document.title.index`. You can customize that with methods like
`getIndexTitle(): ?string` (if using `IndexAction` trait), `getCreateTitle(): ?string` or
`getUpdateTitle(): ?string` (if using `CreateAction` or `UpdateAction` trait) and `getSubTitle(): ?string`

### List

By default, a `DynamicDataGrid` is prepared for the item class specified which simply responds with all documents (non-paginated) in `/list` endpoint.
In order to display the list on frontend, create a `*List.tsx` component to configure `ServerTable` and set `seerverSide` to `false`.
You may use `DeleteButton` and `EditButton` to configure actions column. Delete action will require confirmation.
Register the component in `webcomponents.ts` file and place the tag in your `show.html.twig`

## Customization

### Adding new actions

Since `CrudController` is just a base controller, it is possible to add extra actions by adding methods tagged with `#[Route]` attribute.

### Removing and overriding actions

If you wish to remove or override any action, it is advised to extend `BasicCrudController` and us the traits in `CRUD` namespace.
`BasicCrudController` provides only the common structure for the CRUD, allowing you to customize it even more!

### Template paths

If you wish to store your templates in different path, you may override `getTemplatePrefix()` method accordingly.

### Route names

Route names are generated from the "key" by default. If you wish to change that behaviour, override `getRoutePrefix()` method.

### List

Since we are not able to add any arguments to existing `list()` method, the list itself has been extracted to `getList()` method.
You may use service subscriber to inject the list needed or use property injection.

You may also create a new `customList()` method and copy the routing attributes to override the base class routing instead.
This will allow using controller argument injection.

To learn how to create custom lists (data grids), read `data-grids.md`.
