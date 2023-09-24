import React from "react";

interface Props {
  children: React.ReactNode;
}

const JustifyEnd = (props: Props) => (
  <div className="d-flex justify-content-end">{props.children}</div>
);

export default JustifyEnd;
