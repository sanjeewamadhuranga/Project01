# Accessing AWS services locally

Since according to our policy, we do not create any permanent AWS key pairs, we need to use temporary STS credentials to access AWS services.
This is done by using the [AWS CLI](https://aws.amazon.com/cli/) and the [AWS Vault](https://github.com/99designs/aws-vault) tool.

This is because the keys generated allow only to access the services in SSO account - accessing other accounts
requires switching the role to the appropriate one and using the temporary credentials.

Assuming you have `aws-vault` already configured and the profile you are using is named `_dev`,
simply start your Symfony server using following command:

```bash
aws-vault exec _dev -- symfony serve # You can add -d flag at the end to run in the background
```

To check if you are able to access Cognito, simply go to Federated Identities page:
http://localhost:8000/onboarding/federated-identity/list

This will create temporary credentials for the role specified in your AWS Vault profile for you and pass them to the Symfony server.
Please note that the credentials are only valid for 1 hour, so you will need to re-run the command after that time if needed.
