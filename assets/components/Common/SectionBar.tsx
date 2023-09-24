import React from "react";
import { Card, Row, Col } from "react-bootstrap";

type Props = {
  urlBack?: string;
  children: React.ReactNode | string;
};

const SectionBar: React.FC<Props> = ({ urlBack, children }: Props) => {
  return (
    <Card className="mb-3">
      <Card.Header>
        <Row>
          {urlBack ? (
            <Col>
              <h5>
                <a className="btn" href={urlBack}>
                  <span className="fas fa-arrow-left"></span>
                </a>
                {children}
              </h5>
            </Col>
          ) : (
            children
          )}
        </Row>
      </Card.Header>
    </Card>
  );
};

export default SectionBar;
