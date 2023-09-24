/* eslint-disable @typescript-eslint/no-namespace */
import times from "lodash/times";
import { CaseRow } from "../../components/Lists/Compliance/CaseList";
import { CaseStatus } from "../../components/Lists/Compliance/CaseStatusLabel";
import {
  DisputeRow,
  DisputeState,
} from "../../components/Lists/Compliance/DisputeList";
import { Address, Entity } from "../../models/common";
import { CompanyRow, CompanyStatus } from "../../models/company";
import { TransactionRow, TransactionStatus } from "../../models/transaction";
import { AdministratorRow, Role } from "../../models/user";
import Chance from "chance";

const chance = new Chance();

type FakeAdministratorArgs = {
  lastLogin?: string | null;
  locale?: string;
  enabled?: boolean;
};

global.fake = {
  ...(global.fake ?? {}),
  common: {
    ...(global.fake?.common ?? {}),
    address: (): Address => ({
      address1: chance.address(),
      address2: chance.address(),
      city: chance.city(),
      country: chance.country(),
      postalCode: chance.postcode(),
      state: chance.word(),
    }),
    entity: (): Entity => ({
      id: chance.guid(),
      createdAt: chance.date().toISOString(),
      updatedAt: chance.date().toISOString(),
      deleted: chance.bool(),
    }),
  },
  compliance: {
    ...(global.fake?.compliance ?? {}),
    caseRow: () => {
      const email = chance.url();
      return {
        ...fake.common.entity(),
        company: {
          id: chance.guid(),
          name: chance.name(),
          riskProfile: chance.name(),
        },
        reason: chance.sentence(),
        status: randomEnum(CaseStatus),
        approved: chance.bool(),
        reviewed: chance.bool(),
        handler: null,
        approver: email,
        email,
      };
    },
  },
  company: {
    ...(global.fake?.company ?? {}),
    row: (): CompanyRow => ({
      ...fake.common.entity(),
      tradingName: chance.name().toLowerCase(),
      currency: chance.currency().code,
      usersCount: chance.natural({ max: 1000 }),
      businessEmail: chance.email(),
      address: fake.common.address(),
      status: randomEnum(CompanyStatus),
      subscription: chance.paragraph({ count: 100 }),
    }),
    companyList: (count = 5): CompanyRow[] =>
      times(count, () => fake.company.row()),
  },
  user: {
    ...(global.fake?.user ?? {}),
    role: (): Role => ({
      ...fake.common.entity(),
      name: chance.word(),
      description: chance.paragraph({ sentences: 2 }),
      permissions: times(10, () => chance.word()),
    }),
    roleList: (count = 5): Role[] => times(count, () => fake.user.role()),
    administrator: (
      { lastLogin, locale, enabled }: FakeAdministratorArgs = {
        lastLogin: null,
        locale: "en",
        enabled: true,
      }
    ): AdministratorRow => ({
      ...fake.common.entity(),
      avatar: chance.avatar(),
      email: chance.email(),
      enabled: enabled ?? chance.bool(),
      deleted: chance.bool(),
      suspended: chance.bool(),
      lastLogin: lastLogin ?? chance.date().toISOString(),
      managerPortalRoles: fake.user.roleList(),
      locale: locale ?? chance.locale(),
      googleAuthenticatorEnabled: chance.bool(),
      accountExpirationDate: chance.date().toISOString(),
    }),
    administratorList: (
      count = 5,
      args?: FakeAdministratorArgs
    ): AdministratorRow[] => times(count, () => fake.user.administrator(args)),
  },
  transaction: {
    ...(global.fake?.transaction ?? {}),
    row: (): TransactionRow => ({
      ...fake.common.entity(),
      status: randomEnum(TransactionStatus),
      provider: chance.name(),
      amount: chance.natural({ max: 99999 }),
      currency: chance.currency().code,
      tradingName: chance.name(),
      payAmount: chance.natural({ max: 9999 }),
      payCurrency: chance.currency().code,
      confirmed: chance.date().toISOString(),
    }),
    transactionList: (count = 5): TransactionRow[] =>
      times(count, () => fake.transaction.row()),
  },
  dispute: {
    ...(global.fake?.dispute ?? {}),
    row: (): DisputeRow => ({
      ...fake.common.entity(),
      company: {
        id: chance.guid(),
        name: chance.name(),
      },
      transaction: {
        id: chance.guid(),
        amount: chance.natural(),
        currency: chance.currency().code,
        state: randomEnum(TransactionStatus),
        balance: chance.natural(),
      },
      chargeback: {
        id: chance.guid(),
        amount: chance.natural(),
        currency: chance.currency().code,
        state: randomEnum(TransactionStatus),
        balance: chance.natural(),
      },
      disputeFee: chance.natural(),
      handler: chance.first(),
      state: randomEnum(DisputeState),
      reason: chance.sentence(),
      comments: chance.paragraph({ sentences: 2 }),
      attachments: times(4, () => chance.word()),
    }),
  },
};

declare global {
  namespace fake {
    namespace common {
      const address: () => Address;
      const entity: () => Entity;
    }
    namespace compliance {
      const caseRow: () => CaseRow;
    }
    namespace company {
      const row: () => CompanyRow;
      const companyList: (count?: number) => CompanyRow[];
    }
    namespace user {
      const role: () => Role;
      const roleList: () => Role[];
      const administrator: (args?: FakeAdministratorArgs) => AdministratorRow;
      const administratorList: (
        count?: number,
        args?: FakeAdministratorArgs
      ) => AdministratorRow[];
    }
    namespace transaction {
      const row: () => TransactionRow;
      const transactionList: (count?: number) => TransactionRow[];
    }
    namespace dispute {
      const row: () => DisputeRow;
    }
  }
}

function randomEnum<T extends object>(anEnum: T): T[keyof T] {
  const enumValues = Object.values(anEnum);
  const randomIndex = chance.natural({ max: enumValues.length - 1 });
  return enumValues[randomIndex];
}
