import { useIntl } from "react-intl";
import ServerTable from "../../Table/ServerTable";
import React from "react";
import { SortDirection } from "../../../models/table";
import NoResult from "../../Table/NoResult";
import { Entity } from "../../../models/common";
import CrudActions from "../../Common/CrudActions";
import { SearchableListProps, withSearch } from "../SearchableList";
import CreateButton from "../../Common/CreateButton";
import Routing from "../../../services/routing";

const intlPrefix = "config.bank";
const routeNamePrefix = "configuration_bank";

interface BankRow extends Entity {
  bankCode: string | null;
  bankName: string | null;
  country: string | null;
  branches: Array<BankBranch>;
}

interface BankBranch {
  branchCode: string;
  branchName: string;
  city: string | null;
}

const BankList = (props: SearchableListProps): JSX.Element => {
  const intl = useIntl();
  return (
    <ServerTable<BankRow>
      source={Routing.generate(`${routeNamePrefix}_list`)}
      searchableFields={["bankCode", "bankName", "city", "country"]}
      columns={[
        {
          id: "bankName",
          name: intl.formatMessage({
            id: `${intlPrefix}.bankName`,
            defaultMessage: "Bank name",
          }),
        },
        {
          id: "bankCode",
          name: intl.formatMessage({
            id: `${intlPrefix}.bankCode`,
            defaultMessage: "Bank code",
          }),
        },
        {
          id: "branches",
          name: intl.formatMessage({
            id: `${intlPrefix}.branches`,
            defaultMessage: "Branches",
          }),
          sortable: false,
          render: (branches) => branches.length.toString(),
        },
        {
          id: "country",
          name: intl.formatMessage({
            id: `${intlPrefix}.country`,
            defaultMessage: "Country",
          }),
        },
        {
          id: "id",
          name: "",
          sortable: false,
          render: (id): JSX.Element => (
            <CrudActions
              routeNamePrefix={routeNamePrefix}
              title={"bank"}
              id={id}
            />
          ),
        },
      ]}
      sort={{ column: "bankName", direction: SortDirection.asc }}
      extraData={{ filters: { search: props.search } }}
      noResult={
        <NoResult
          header={intl.formatMessage({
            id: `${intlPrefix}.noBank.header`,
            defaultMessage: "No bank found",
          })}
          body={intl.formatMessage({
            id: `${intlPrefix}.noBank.body`,
            defaultMessage: "Banks will appear here",
          })}
        />
      }
    />
  );
};

export default withSearch(BankList, "Banks", () => (
  <CreateButton routeNamePrefix={routeNamePrefix} />
));
