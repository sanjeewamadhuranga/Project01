## Summary

Previously, we need to create some sort of spy class in order to mock services for feature test
With Happyr Service Mockery, we do not need to create spy classes and are able to mock the object instantly.

## How to use

1. Register the class in `happyr_service_mocking.yaml`.
    For example :
    ```yaml
    happyr_service_mocking:
        services:
            - 'App\Application\Queue\Bus'
   ```
3. Based on the nature of the test case,  you can use either `ServiceMock::all` or `SeviceMock::next` or `ServiceMock::swap`or, etc.
4. You find in-depth documentation [here](https://github.com/Happyr/service-mocking#configure-services)
