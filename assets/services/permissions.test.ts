import { checkPermission } from "./permissions";
import settings from "./settings";

type PermissionMockType = {
  title: string;
  wants: string | string[];
  has: string | string[];
  receives: boolean; // granted
};

const permissionMockedData: PermissionMockType[] = [
  {
    title: "Full match access",
    wants: "offers.benefits.*",
    has: "offers.benefits.*",
    receives: true, // granted
  },
  {
    title: "Admin can access same module",
    wants: "offers.benefits.view",
    has: "offers.benefits.*",
    receives: true,
  },
  {
    title: "Admin should not access different module",
    wants: "offers.cards.view",
    has: "offers.benefits.*",
    receives: false,
  },
  {
    title: "User with view access only should only access to view module",
    wants: "offers.benefits.view",
    has: "offers.benefits.view",
    receives: true,
  },
  {
    title:
      "User who has permission to view module(A) should not get permission to view module(b)",
    wants: "offers.benefits.view",
    has: "offers.cards.view",
    receives: false,
  },
  {
    title: "Admin who permission has (*) should access to entire system",
    wants: "something.test",
    has: "*",
    receives: true,
  },
  {
    title:
      "Admin who permission has (*) should access to any other wildcard permission",
    wants: "something.*",
    has: "*",
    receives: true,
  },
  {
    title:
      "Admin who has multiple permissions that does not include module permission",
    wants: "offers.benefits.*",
    has: ["offers.locations.*", "offers.something.*"],
    receives: false,
  },
  {
    title:
      "Admin who has multiple permissions that does include exact module permission",
    wants: "offers.benefits.*",
    has: ["offers.locations.*", "offers.benefits.*"],
    receives: true,
  },
  {
    title:
      "Admin who has multiple permission that has one view permission for the module",
    wants: "offers.benefits.view",
    has: ["offers.benefits.view", "offers.cards.view"],
    receives: true,
  },
  {
    title:
      "User with at least one permission matching wildcard should be granted",
    wants: "offers.benefits.*",
    has: ["offers.benefits.view", "offers.cards.view"],
    receives: true,
  },
  {
    title: "User with no permission matching wildcard should be denied",
    wants: "offers.benefits.*",
    has: ["offers.cards.view", "offers.brand.view"],
    receives: false,
  },
  {
    title:
      "Wildcard inside the attribute should grant access if user has at least one matching permission",
    wants: "offers.*.view",
    has: ["offers.cards.view", "offers.brand.view"],
    receives: true,
  },
  {
    title:
      "Wildcard inside the attribute should deny access if user does not have any matching permission",
    wants: "offers.*.delete",
    has: ["offers.cards.view", "offers.brand.view"],
    receives: false,
  },
  {
    title: "Dots are properly escaped in the attribute",
    wants: "offers.*.view",
    has: ["offers.locationaview"],
    receives: false,
  },
  {
    title: "Dots are properly escaped in permission list",
    wants: "offers.*aview",
    has: ["offers.cards.view"],
    receives: false,
  },
];

describe("test permission checking", () => {
  test.each(permissionMockedData)("$title | expected: $receives", (perm) => {
    settings.permissions = ([] as string[]).concat(perm.has);
    expect(checkPermission(perm.wants)).toBe(perm.receives);
  });
});
