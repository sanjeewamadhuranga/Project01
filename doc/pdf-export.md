# PDF export

In order to provide PDF export functionality, an external tool must be installed in the system:
[wkhtmltopdf](https://wkhtmltopdf.org/) - make sure you install QT patched version.

[KnpSnappyBundle](https://github.com/KnpLabs/KnpSnappyBundle) has been introduced for developer convenience
and smooth integration with the app.

## Configuration

Besides of standard configuration (pointing `WKHTMLTOPDF_PATH` environment variable to the absolute path of the binary),
one must set `INTERNAL_ASSET_URL` environment variable to the **internal** URL for assets to allow wkhtmltopdf fetching
assets (see next chapter).

## Internal vs external URLs

Because external URLs (like ``) are pointing to a load balancer and the access
is limited only to specific IPs, therefore the app container cannot reach its own files using that external domain.
Instead, an internal URL should be used (like `http://localhost/`) which should provide the access to these assets
without such limitations. All static assets should be referenced using that internal domain when using `wkhtmltopdf`.

Another option would be to allow `wkhtmltopdf` to access local files but that creates a potential security risk as
malicious user input could lead to expose the files from the container, therefore we did not pursue that path.

The switch between external and internal domain is done using `<base href="domain">` tag in `pdf.html.twig` which is
controlled by optional `internal_url: bool` parameter passed to Twig template.

## Testing

Because it is not easy to replace the service in the container, a dummy `PdfSpy` class is used instead of vendor's `Pdf`
class which stores the parameters it was called with. Consider following example:

```php
self::$client->request('GET', '/pdf/export');   // Trigger a PDF export
$pdf = self::getContainer()->get(Pdf::class);   // Get PdfSpy from the container
self::assertInstanceOf(PdfSpy::class, $pdf);
$content = $pdf->getCalls()[0];                 // $content contains the HTML content passed to the PDF generator
$crawler = new Crawler($content);               // Feed the Crawler with the parameters from first
```
