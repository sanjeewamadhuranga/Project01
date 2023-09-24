import React from "react";
import { Button } from "react-bootstrap";

export interface Props {
  header: string;
  body: string;
  buttonText?: string;
  buttonLink?: string;
}

const NoResult: React.FC<Partial<Props>> = (props: Partial<Props>) => (
  <div className="padding-10-em">
    <h1>{props.header}</h1>
    <p>{props.body}</p>
    {props.buttonText !== undefined && props.buttonLink !== undefined && (
      <Button
        href={props.buttonLink}
        variant="outline-secondary"
        className="me-1 mb-1"
      >
        {props.buttonText}
      </Button>
    )}
  </div>
);

NoResult.defaultProps = {
  header: "No results",
  body: "",
};

export default NoResult;
