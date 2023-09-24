import React from "react";
import { useIntl } from "react-intl";
import ServerTable from "../../Table/ServerTable";
import { SortDirection } from "../../../models/table";
import DateFormatter from "../../Common/DateFormatter";
import NoResult from "../../Table/NoResult";
import { Entity } from "../../../models/common";
import DeleteButton from "../../Modals/DeleteButton";
import JustifyEnd from "../../Common/JustifyEnd";
import Routing from "../../../services/routing";

interface Props {
  disputeid: string;
}

export interface NoteRow extends Entity {
  detail: string;
  email: string;
}

const intlPrefix = "dispute_notes.list";

const DisputeNoteList = (props: Props): JSX.Element => {
  const intl = useIntl();
  return (
    <ServerTable<NoteRow>
      source={`/compliance/disputes/${props.disputeid}/list-notes`}
      serverSide={false}
      columns={[
        {
          id: "detail",
          name: intl.formatMessage({
            id: `${intlPrefix}.detail`,
            defaultMessage: "detail",
          }),
        },
        {
          id: "email",
          name: intl.formatMessage({
            id: `${intlPrefix}.email`,
            defaultMessage: "Email",
          }),
        },
        {
          id: "createdAt",
          name: intl.formatMessage({
            id: `${intlPrefix}.created`,
            defaultMessage: "Created",
          }),
          render: (createdAt) => <DateFormatter date={createdAt} />,
        },
        {
          id: "id",
          sortable: false,
          name: "",
          render: (id: string): JSX.Element => (
            <JustifyEnd>
              <DeleteButton
                deleteUrl={Routing.generate(`compliance_dispute_delete`, {
                  id: props.disputeid,
                  noteId: id,
                })}
                title={intl.formatMessage({
                  id: `${intlPrefix}.note`,
                })}
              />
            </JustifyEnd>
          ),
        },
      ]}
      entryName="data"
      sort={{ column: "createdAt", direction: SortDirection.desc }}
      noResult={
        <NoResult
          header={intl.formatMessage({
            id: `${intlPrefix}.noNotes.header`,
            defaultMessage: "No Notes",
          })}
          body={intl.formatMessage({
            id: `${intlPrefix}.noNotes.body`,
            defaultMessage: "Notes added will appear here",
          })}
        />
      }
    />
  );
};

export default DisputeNoteList;
