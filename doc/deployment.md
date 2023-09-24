# Deployment

This application is deployed to different AWS accounts assigned to individual tenants/environments and each of them
may have a different version deployed.

## Triggering a deployment

In order to trigger a deployment, one should create a new tag on `main` branch (remember to pull the changes first):

```shell
git checkout main && git pull origin main
git tag v0.2.0 # Change this
git push --tags
```

This will trigger a `build-deploy` pipeline on CircleCI which can be accessed via https://app.circleci.com.

After the `pack` step is complete (~4 min), the job will be awaiting user interaction to proceed.
Team member responsible for the deployment should use CircleCI web interface to click <kbd>Approve</kbd> next to the
environment name they wish to deploy to. Doing so will trigger a `deploy_*` job (~2 min) which updates the application
on selected environment.

## Deployment process details

Deployment consists of 3 steps:

- `pack` - building and pushing Docker image to ECR in `shared` account
- `approve_*` - awaiting user approval
- `deploy_*` - create a new ECS TaskDefinition revision and tell the ECS cluster to use it

### `pack` step

The `pack` step is responsible for building an application artifact (Docker image) and pushing it to ECR.

1. Install dependencies & build `routes.json` file.
2. Build frontend assets.
3. Notify Sentry about the release, attaching source maps.
4. Write current version ID (commit SHA) to `.env.local`.
5. Build a Docker image (see `./Dockerfile`) and push it to ECR.
6. Build a ZIP file and store it in job artifacts (only for reference - not used).

Notes:

- Docker image is tagged using **both** current commit SHA (``)
and `latest` (``) so the last built image is always available under same tag.
- ZIP file is not used for deployment (anymore) and is prepared there only for reference.

### `deploy_*` steps

Each environment has a separate job that deploys the app to its Elastic Container Service cluster.

1. Set variables based on environment name (see `.circleci/prepare-env.sh`).
2. Switch role based on above (`arn:aws:iam::${account-id}:role/circleci_role_${configuration}_${environment}`).
3. Use [ecs-deploy](https://github.com/silinternational/ecs-deploy) script to deploy current SHA to the cluster.
4. Set `/${CONFIGURATION}/${ENVIRONMENT}` SSM parameter to current SHA to keep the state.
5. Notify Sentry about the deployment to specific environment.

Notes:

- `/${CONFIGURATION}/${ENVIRONMENT}` SSM parameter stores currently deployed version to make sure
that the application will not be updated to newer version if infrastructure changes occur (in CloudFormation templates).

## Adding a new environment

Because `.circleci/config.yml` uses many YAML templating features, adding the new environment requires only:

1. Adding the environment "slug" to `- &environment_names: ["pom_dev", ...]` line in `.circleci/config.yml`.
2. Adding a new case to `.circleci/prepare-env.sh` mapping the slug to a set of environment variables.
3. Triggering a new deployment to verify the validity of the variables.
