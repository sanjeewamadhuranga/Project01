import { SearchableListProps, withSearch } from "../SearchableList";
import ServerTable from "../../Table/ServerTable";
import NoResult from "../../Table/NoResult";
import React from "react";
import { SortDirection } from "../../../models/table";
import CreateButton from "../../Common/CreateButton";
import CrudActions from "../../Common/CrudActions";
import { Entity } from "../../../models/common";
import { useIntl } from "react-intl";
import Routing from "../../../services/routing";
import DateFormatter from "../../Common/DateFormatter";
import DetailsButton from "../../Common/DetailsButton";

const routeNamePrefix = "compliance_risk_profile";

interface RiskProfileRow extends Entity {
  code: string;
}

const intlPrefix = "compliance.riskProfile";

const RiskProfileList = (props: SearchableListProps): JSX.Element => {
  const intl = useIntl();
  return (
    <ServerTable<RiskProfileRow>
      source={Routing.generate(`${routeNamePrefix}_list`)}
      serverSide={false}
      searchableFields={["code"]}
      columns={[
        {
          id: "code",
          name: intl.formatMessage({
            id: `${intlPrefix}.code`,
            defaultMessage: "Risk profile",
          }),
          render: (code, riskProfileRow: { id: string }): JSX.Element => (
            <a
              href={Routing.generate(`${routeNamePrefix}_show`, {
                id: riskProfileRow?.id,
              })}
            >
              {code}
            </a>
          ),
        },
        {
          id: "createdAt",
          name: intl.formatMessage({
            id: `${intlPrefix}.createdAt`,
            defaultMessage: "Date created",
          }),
          render: (createdAt) => <DateFormatter date={createdAt} />,
        },
        {
          id: "id",
          sortable: false,
          name: "",
          render: (id: string): JSX.Element => (
            <CrudActions
              title={intl.formatMessage({
                id: `${intlPrefix}.riskProfile`,
              })}
              routeNamePrefix={routeNamePrefix}
              id={id}
            />
          ),
        },
        {
          id: "id",
          name: "",
          sortable: false,
          render: (id): JSX.Element => (
            <DetailsButton id={id} routeNamePrefix={routeNamePrefix} />
          ),
        },
      ]}
      sort={{ column: "createdAt", direction: SortDirection.desc }}
      noResult={
        <NoResult
          header={intl.formatMessage({
            id: `${intlPrefix}.noRiskProfile.header`,
            defaultMessage: "No risk profile found",
          })}
          body={intl.formatMessage({
            id: `${intlPrefix}.noRiskProfile.body`,
            defaultMessage: "Risk profiles will appear here",
          })}
        />
      }
      {...props}
    />
  );
};

export default withSearch(RiskProfileList, "Risk profiles", () => (
  <CreateButton routeNamePrefix={routeNamePrefix} />
));
