import React, { ElementType } from "react";
import { checkPermission } from "../../services/permissions";

export function withPermissionCheck(
  WrappedComponent: ElementType,
  permissionType: string
): ElementType {
  const Hoc = (props) =>
    checkPermission(permissionType) ? <WrappedComponent {...props} /> : null;

  Hoc.displayName = "WithPermission";

  return Hoc;
}
