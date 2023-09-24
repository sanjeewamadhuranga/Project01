import React from "react";
import NoResult from "../../Table/NoResult";
import ServerTable from "../../Table/ServerTable";
import CrudActions from "../../Common/CrudActions";
import { SortDirection } from "../../../models/table";
import { withSearch, SearchableListProps } from "../SearchableList";
import CreateButton from "../../Common/CreateButton";
import BoolLabel from "../../Table/BoolLabel";
import { Entity } from "../../../models/common";
import { useIntl } from "react-intl";
import Routing from "../../../services/routing";
import DetailsButton from "../../Common/DetailsButton";

interface SubscriptionPlan extends Entity {
  name: string;
  code: string;
  description: string | null;
  modification: string;
  userPublic: boolean;
  instantActivation: boolean;
  dccMarkupRate: number;
  dccMerchantRebateRate: number;
}

const routePrefix = "/configuration/subscription-plans";
const routeNamePrefix = "configuration_subscription_plan";
const intlPrefix = "configuration.subscriptionPlan";

const SubscriptionPlanList = (props: SearchableListProps): JSX.Element => {
  const intl = useIntl();
  return (
    <ServerTable<SubscriptionPlan>
      source={Routing.generate(`${routeNamePrefix}_list`)}
      serverSide={false}
      searchableFields={["name", "code", "description", "modification"]}
      columns={[
        {
          id: "name",
          name: intl.formatMessage({
            id: `${intlPrefix}.name`,
            defaultMessage: "Name",
          }),
          render: (name, plan): JSX.Element => (
            <a href={`${routePrefix}/${plan.id}`}>{name}</a>
          ),
        },
        {
          id: "code",
          name: intl.formatMessage({
            id: `${intlPrefix}.code`,
            defaultMessage: "Code",
          }),
        },
        {
          id: "description",
          name: intl.formatMessage({
            id: `${intlPrefix}.description`,
            defaultMessage: "Description",
          }),
        },
        {
          id: "modification",
          name: intl.formatMessage({
            id: `${intlPrefix}.modification`,
            defaultMessage: "Modification",
          }),
        },
        {
          id: "userPublic",
          name: intl.formatMessage({
            id: `${intlPrefix}.userPublic`,
            defaultMessage: "Public",
          }),
          render: (userPublic: boolean): JSX.Element => (
            <BoolLabel value={userPublic} />
          ),
        },
        {
          id: "instantActivation",
          name: intl.formatMessage({
            id: `${intlPrefix}.instantActivation`,
            defaultMessage: "Instant activation",
          }),
          render: (instantActivation: boolean): JSX.Element => (
            <BoolLabel value={instantActivation} />
          ),
        },
        {
          id: "dccMarkupRate",
          name: intl.formatMessage({
            id: `${intlPrefix}.dccMarkupRate`,
            defaultMessage: "DCC markup rate",
          }),
          render: (percentage: number) => `${percentage ?? 0}%`,
        },
        {
          id: "dccMerchantRebateRate",
          name: intl.formatMessage({
            id: `${intlPrefix}.dccMerchantRebateRate`,
            defaultMessage: "DCC rebate rate",
          }),
          render: (percentage: number) => `${percentage ?? 0}%`,
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
                id: `${intlPrefix}.subscriptionPlan`,
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
      sort={{ column: "name", direction: SortDirection.asc }}
      noResult={
        <NoResult
          header={intl.formatMessage({
            id: `${intlPrefix}.noSubscriptionPlan.header`,
            defaultMessage: "No subscription plans found",
          })}
          body={intl.formatMessage({
            id: `${intlPrefix}.noSubscriptionPlan.body`,
            defaultMessage: "Added subscription plans will appear here",
          })}
        />
      }
      {...props}
    />
  );
};

export default withSearch(SubscriptionPlanList, "Subscription Plans", () => (
  <CreateButton routeNamePrefix={routeNamePrefix} />
));
