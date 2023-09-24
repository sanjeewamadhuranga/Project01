import React from "react";
import NoResult from "../../Table/NoResult";
import ServerTable from "../../Table/ServerTable";
import CrudActions from "../../Common/CrudActions";
import BoolLabel from "../../Table/BoolLabel";
import { SearchableListProps, withSearch } from "../SearchableList";
import CreateButton from "../../Common/CreateButton";
import { useIntl } from "react-intl";
import Routing from "../../../services/routing";
import DetailsButton from "../../Common/DetailsButton";

const routePrefix = "/configuration/mdr-billing";
const routeNamePrefix = "configuration_mdr_billing";
const intlPrefix = "configuration.mdrBilling";

const MdrBillingList = (props: SearchableListProps): JSX.Element => {
  const intl = useIntl();
  return (
    <ServerTable
      source={Routing.generate(`${routeNamePrefix}_list`)}
      searchableFields={["mdr", "fixedFeeCurrency", "remittance"]}
      serverSide={false}
      columns={[
        {
          id: "mdr",
          name: intl.formatMessage({
            id: `${intlPrefix}.mdr`,
            defaultMessage: "MDR",
          }),
          render: (mdr, billing) => (
            <a href={`${routePrefix}/${billing.id}`}>{mdr}</a>
          ),
        },
        {
          id: "remittance",
          name: intl.formatMessage({
            id: `${intlPrefix}.remittance`,
            defaultMessage: "Remittance",
          }),
          render: (remittance: boolean): JSX.Element => (
            <BoolLabel value={remittance} />
          ),
        },
        {
          id: "fixedFeeCurrency",
          name: intl.formatMessage({
            id: `${intlPrefix}.remittance`,
            defaultMessage: "Fixed fee currency",
          }),
        },
        {
          id: "processingFixed",
          name: intl.formatMessage({
            id: `${intlPrefix}.processingFixed`,
            defaultMessage: "Processing fixed",
          }),
          render: (fixed: number) => `${fixed / 100}`,
        },
        {
          id: "processingPercentage",
          name: intl.formatMessage({
            id: `${intlPrefix}.processingPercentage`,
            defaultMessage: "Processing percentage",
          }),
          render: (percentage: number) => `${percentage ?? 0}%`,
        },
        {
          id: "platformFixed",
          name: intl.formatMessage({
            id: `${intlPrefix}.platformFixed`,
            defaultMessage: "Platform fixed",
          }),
          render: (fixed: number) => `${fixed / 100}`,
        },
        {
          id: "platformPercentage",
          name: intl.formatMessage({
            id: `${intlPrefix}.platformPercentage`,
            defaultMessage: "Platform percentage",
          }),
          render: (percentage: number) => `${percentage}%`,
        },
        {
          id: "id",
          sortable: false,
          name: intl.formatMessage({
            id: `${intlPrefix}.action`,
            defaultMessage: "Action",
          }),
          render: (id: string): JSX.Element => (
            <CrudActions
              title={intl.formatMessage({
                id: `${intlPrefix}.mdrBilling`,
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
      noResult={
        <NoResult
          header={intl.formatMessage({
            id: `${intlPrefix}.noBilling.header`,
            defaultMessage: "No MDR billings found",
          })}
          body={intl.formatMessage({
            id: `${intlPrefix}.noBilling.body`,
            defaultMessage: "MDR billings added will appear here",
          })}
        />
      }
      {...props}
    />
  );
};

export default withSearch(MdrBillingList, "MDR Billing", () => (
  <CreateButton routeNamePrefix={routeNamePrefix} />
));
