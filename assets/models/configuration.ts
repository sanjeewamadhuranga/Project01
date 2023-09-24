import { Entity } from "./common";

export interface RoleCompany extends Entity {
  name: string;
}

export interface MerchantRoleRow extends Entity {
  name: string;
  description: string;
  companies: Array<RoleCompany>;
  default: boolean;
}

export interface LogRow extends Entity {
  id: string;
  action: string | null;
  changeSets: Array<LogChangeSet> | null;
  created: string;
  user: string | null;
  details: LogDetails | null;
  objectId: string | null;
  objectClass: string | null;
}

export interface NotificationRow extends Entity {
  sent: boolean;
  title: string | null;
  message: string;
  recipient: string | null;
  sub: string | null;
  companyId: string | null;
  companyName: string | null;
  metadata: Record<string, unknown>;
}

interface LogChangeSet {
  field: string;
  changes: [] | null;
}

interface LogDetails {
  username: string;
  browser: string;
  ip: string;
}
