import React, { useEffect, useState } from "react";
import { Col, Row } from "react-bootstrap";
import NumberStats from "../../Dashboard/Widgets/NumberStats";
import axios from "axios";
import { FormattedMessage } from "react-intl";

interface ComplianceCasesStats {
  open: number;
  inReview: number;
  inApproval: number;
  closed: number;
}

export interface CaseSummaryProps {
  setFilterByFieldValue: (value: string) => void;
  summarySource: string;
}

const CaseSummary = (props: CaseSummaryProps): JSX.Element => {
  const [stats, setStats] = useState<ComplianceCasesStats | null>(null);
  const [isLoading, setIsLoading] = useState<boolean>(true);

  useEffect(() => {
    axios.get(props.summarySource).then((response) => {
      setIsLoading(false);
      setStats(response.data);
    });
  }, []);

  return (
    <Row className="summary-cards">
      <Col sm={3} className="card-column">
        <NumberStats
          isLoading={isLoading}
          header="compliance.case.header.openCases"
          value={stats?.open ?? 0}
          linkElement={
            <a
              href="#"
              className="font-weight-semi-bold text-nowrap fs--1"
              onClick={(e) => {
                e.preventDefault();
                props.setFilterByFieldValue("open");
              }}
            >
              <FormattedMessage id="compliance.case.actions.showCases" />{" "}
              <span className="fas fa-chevron-right" />
            </a>
          }
        />
      </Col>
      <Col sm={3} className="card-column">
        <NumberStats
          isLoading={isLoading}
          header="compliance.case.header.inReview"
          value={stats?.inReview ?? 0}
          linkElement={
            <a
              href="#"
              className="font-weight-semi-bold text-nowrap fs--1"
              onClick={(e) => {
                e.preventDefault();
                props.setFilterByFieldValue("in_review");
              }}
            >
              <FormattedMessage id="compliance.case.actions.showCases" />{" "}
              <span className="fas fa-chevron-right" />
            </a>
          }
        />
      </Col>
      <Col sm={3} className="card-column">
        <NumberStats
          isLoading={isLoading}
          header="compliance.case.header.inApproval"
          value={stats?.inApproval ?? 0}
          linkElement={
            <a
              href="#"
              className="font-weight-semi-bold text-nowrap fs--1"
              onClick={(e) => {
                e.preventDefault();
                props.setFilterByFieldValue("in_approval");
              }}
            >
              <FormattedMessage id="compliance.case.actions.showCases" />{" "}
              <span className="fas fa-chevron-right" />
            </a>
          }
        />
      </Col>
      <Col sm={3} className="card-column">
        <NumberStats
          isLoading={isLoading}
          header="compliance.case.header.closed"
          value={stats?.closed ?? 0}
          linkElement={
            <a
              href="#"
              className="font-weight-semi-bold text-nowrap fs--1"
              onClick={(e) => {
                e.preventDefault();
                props.setFilterByFieldValue("closed");
              }}
            >
              <FormattedMessage id="compliance.case.actions.showCases" />{" "}
              <span className="fas fa-chevron-right" />
            </a>
          }
        />
      </Col>
    </Row>
  );
};

export default CaseSummary;
