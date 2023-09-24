import React from "react";
import BoolLabel from "../../Table/BoolLabel";
import ServerTable from "../../Table/ServerTable";
import NoResult from "../../Table/NoResult";
import { useIntl } from "react-intl";
import { NotificationRow } from "../../../models/configuration";
import NotificationDetailsModal from "../../Modals/NotificationDetailsModal";
import DateFormatter from "../../Common/DateFormatter";
import { SortDirection } from "../../../models/table";

interface Props {
  source: string;
  extraData: Record<string, unknown>;
  search?: string;
}

const intlPrefix = "notification";

const NotificationList = (props: Props): JSX.Element => {
  const intl = useIntl();
  return (
    <ServerTable<NotificationRow>
      source={props.source}
      columns={[
        {
          id: "companyId",
          name: intl.formatMessage({
            id: `${intlPrefix}.company`,
            defaultMessage: "Company",
          }),
          render: (companyId, row) =>
            companyId !== null ? (
              <a href={`/merchants/${companyId}`}>{row.companyName}</a>
            ) : (
              "-"
            ),
        },
        {
          id: "sub",
          name: intl.formatMessage({
            id: `${intlPrefix}.sub`,
            defaultMessage: "SUB",
          }),
          render: (sub) =>
            sub ? (
              <a href={`/onboarding/federated-identity/${sub}`}>{sub}</a>
            ) : (
              "-"
            ),
        },
        {
          id: "sent",
          name: intl.formatMessage({
            id: `${intlPrefix}.sent`,
            defaultMessage: "Sent",
          }),
          render: (sent: boolean): JSX.Element => <BoolLabel value={sent} />,
        },
        {
          id: "title",
          name: intl.formatMessage({
            id: `${intlPrefix}.title`,
            defaultMessage: "Title",
          }),
        },
        {
          id: "message",
          name: intl.formatMessage({
            id: `${intlPrefix}.message`,
            defaultMessage: "Message",
          }),
        },
        {
          id: "createdAt",
          name: intl.formatMessage({
            id: `${intlPrefix}.createdAt`,
            defaultMessage: "Created at",
          }),
          render: (createdAt) => <DateFormatter date={createdAt} />,
        },
        {
          id: "metadata",
          name: "",
          sortable: false,
          render: (metadata) => (
            <NotificationDetailsModal metadata={metadata} />
          ),
        },
      ]}
      extraData={{ filters: { search: props.search, ...props.extraData } }}
      sort={{ column: "createdAt", direction: SortDirection.desc }}
      noResult={
        <NoResult
          header={intl.formatMessage({
            id: `${intlPrefix}.noNotification.header`,
            defaultMessage: "No messages",
          })}
          body={intl.formatMessage({
            id: `${intlPrefix}.noNotification.body`,
            defaultMessage: "Sent messages will appear here",
          })}
        />
      }
    />
  );
};
export default NotificationList;

interface FilterProps {
  companyid: string;
  userid?: string;
  search?: string;
}

export const SmsList = (props: FilterProps): JSX.Element => (
  <NotificationList
    source="/notifications/sms/list"
    search={props.search}
    extraData={{ company: props.companyid, userId: props.userid }}
  />
);

export const EmailList = (props: FilterProps): JSX.Element => (
  <NotificationList
    source="/notifications/email/list"
    search={props.search}
    extraData={{ company: props.companyid, userId: props.userid }}
  />
);

export const PushNotificationList = (props: FilterProps): JSX.Element => (
  <NotificationList
    source="/notifications/push/list"
    search={props.search}
    extraData={{ company: props.companyid, userId: props.userid }}
  />
);
