import ServerTable from "../../Table/ServerTable";
import NoResult from "../../Table/NoResult";
import React, { useState } from "react";
import { SortDirection } from "../../../models/table";
import { Entity } from "../../../models/common";
import CaseActionButton from "./CaseActionButton";
import ComplianceCaseStatusModal from "../../Modals/ComplianceCaseStatusModal";
import CaseStatusLabel, { CaseStatus } from "./CaseStatusLabel";
import { useIntl } from "react-intl";
import DateFormatter from "../../Common/DateFormatter";
import Routing from "../../../services/routing";
import DeleteButton from "../../Modals/DeleteButton";

const routePrefix = "/compliance/case";

interface CaseRowCompany {
  id: string;
  name: string;
  riskProfile: string | null;
  riskProfileId?: string | null;
}

export interface CaseRow extends Entity {
  company: CaseRowCompany;
  reason: string;
  status: CaseStatus;
  approved: boolean;
  reviewed: boolean;
  handler: string | null;
  approver: string | null;
  email: string | null;
}

export interface Props {
  extraData: Record<string, unknown>;
}

const intlPrefix = "compliance.case.list";
const routeNamePrefix = "compliance_case";

const CaseList = ({ extraData }: Props): JSX.Element => {
  const intl = useIntl();

  return (
    <ServerTable<CaseRow>
      source={`${routePrefix}/list`}
      searchableFields={["id"]}
      columns={[
        {
          id: "reason",
          name: "Case ID and Reason",
          render: (reason, caseRow): JSX.Element => (
            <ReasonWithDetails caseRow={caseRow} />
          ),
        },
        {
          id: "createdAt",
          name: intl.formatMessage({
            id: `${intlPrefix}.createdAt`,
            defaultMessage: "Created on",
          }),
          render: (createdAt) => <DateFormatter date={createdAt} />,
        },
        {
          id: "company",
          name: intl.formatMessage({
            id: `${intlPrefix}.company`,
            defaultMessage: "Company and Risk profile",
          }),
          render: (company): JSX.Element => (
            <>
              <a href={`/merchants/${company.id}`}>{company.name}</a>
              <br />
              <a href={`/compliance/risk-profiles/${company.riskProfileId}`}>
                {company.riskProfile}
              </a>
            </>
          ),
        },
        {
          id: "status",
          name: intl.formatMessage({
            id: `${intlPrefix}.status`,
            defaultMessage: "Status",
          }),
          sortable: false,
          render: (status, caseRow): JSX.Element => (
            <CaseStatusWithModal caseRow={caseRow} />
          ),
        },
        {
          id: "id",
          name: "",
          sortable: false,
          render: (id, caseRow): JSX.Element => (
            <div className="d-flex">
              <DeleteButton
                deleteUrl={Routing.generate(`${routeNamePrefix}_delete`, {
                  id: id,
                })}
                title="compliance case"
              />
              <CaseActionButton
                id={caseRow?.id}
                routePrefix={routePrefix}
                status={caseRow.status}
                email={caseRow.email}
              />
            </div>
          ),
        },
      ]}
      sort={{ column: "createdAt", direction: SortDirection.desc }}
      extraData={{ filters: extraData }}
      noResult={
        <NoResult
          header={intl.formatMessage({
            id: `${intlPrefix}.noCase.header`,
            defaultMessage: "No case found",
          })}
          body={intl.formatMessage({
            id: `${intlPrefix}.noCase.body`,
            defaultMessage: "Cases will appear here",
          })}
        />
      }
    />
  );
};

export default React.memo(CaseList);

type CaseRowProps = {
  caseRow: CaseRow;
};

const ReasonWithDetails = ({ caseRow }: CaseRowProps) => {
  return (
    <>
      <div>
        <a
          className="cursor-pointer"
          href={Routing.generate(`${routeNamePrefix}_show`, {
            id: caseRow?.id,
          })}
        >
          {caseRow.id}
        </a>
        <div className={"small"}>{caseRow?.reason}</div>
      </div>
    </>
  );
};

const CaseStatusWithModal = ({ caseRow }: CaseRowProps) => {
  const [showTimeline, setShowTimeline] = useState(false);
  return (
    <>
      <CaseStatusLabel
        case={caseRow}
        showTimeline={() => setShowTimeline(true)}
      />
      <ComplianceCaseStatusModal
        caseRow={caseRow}
        showTimeline={showTimeline}
        hideModal={() => setShowTimeline(false)}
      />
    </>
  );
};
