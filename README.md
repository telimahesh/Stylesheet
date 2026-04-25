# Stylesheet

## GitHub Actions deploy configuration

The workflow `.github/workflows/main.yml` validates deploy configuration before attempting production deploy.

Set these repository or environment variables:
- `AWS_REGION` (example: `us-east-1`, **not** `MY_AWS_REGION`)
- `ECS_CLUSTER`
- `ECS_SERVICE`
- `ECS_TASK_DEFINITION`

Set this secret:
- `AWS_ROLE_TO_ASSUME`

If deploy variables are missing, the workflow will skip deploy and only run PHP lint.
