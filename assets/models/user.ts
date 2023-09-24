import { Entity } from "./common";

export interface Role extends Entity {
  name: string;
  description: string;
  permissions: string[];
}

export interface AdministratorRow extends Entity {
  email: string;
  avatar: string;
  enabled: boolean;
  deleted: boolean;
  suspended: boolean;
  lastLogin: string | null;
  managerPortalRoles: Role[];
  locale: string | null;
  googleAuthenticatorEnabled: boolean;
  accountExpirationDate: string | null;
}
