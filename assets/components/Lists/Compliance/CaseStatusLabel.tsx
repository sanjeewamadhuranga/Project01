import React from "react";
import { Badge } from "react-bootstrap";
import { FormattedMessage } from "react-intl";
import { CaseRow } from "./CaseList";

export enum CaseStatus {
  open = "open",
  in_review = "in_review",
  in_approval = "in_approval",
  closed = "closed",
}

export const caseClassMap = {
  [CaseStatus.open]: "badge-soft-info",
  [CaseStatus.in_review]: "badge-soft-warning",
  [CaseStatus.in_approval]: "badge-soft-success",
  [CaseStatus.closed]: "badge-soft-dark",
};

export const caseDetailsRouteNameSuffix = {
  [CaseStatus.open]: "_assign_review",
  [CaseStatus.in_review]: "_review",
  [CaseStatus.in_approval]: "_approve",
  [CaseStatus.closed]: "_show",
};

interface Props {
  case: CaseRow;
  showTimeline: () => void;
}

const intlPrefix = "compliance.case";

const CaseStatusLabel = (props: Props): JSX.Element => {
  return (
    <>
      <Badge
        onClick={props.showTimeline}
        className={`${caseClassMap[props.case.status]} cursor-pointer`}
        bg=""
      >
        <FormattedMessage id={`${intlPrefix}.status.${props.case.status}`} />
      </Badge>
      <div className="ms-2">
        {props?.case?.email || props?.case?.status === "closed" ? (
          props?.case?.email
        ) : (
          <FormattedMessage id={`${intlPrefix}.status.notAssigned`} />
        )}
      </div>
    </>
  );
};

export default CaseStatusLabel;
