import settings from "./settings";

export function checkPermission(requiredPerm: string | string[]): boolean {
  const requiredPermissions: string[] = ([] as string[]).concat(requiredPerm);

  const getPattern = (permission: string): string =>
    `^${permission.replace(/\./g, "\\.").replace(/\*/g, ".*")}$`;

  return (
    settings.permissions?.some((permission) =>
      requiredPermissions.some(
        (req) =>
          !!req.match(getPattern(permission)) ||
          !!permission.match(getPattern(req))
      )
    ) ?? false
  );
}
